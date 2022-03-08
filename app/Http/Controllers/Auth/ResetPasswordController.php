<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\MySMSPH;
use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Helpers\Credential;
use Seshac\Otp\Otp;
use App\Models\OtpModel;
use App\Models\Auth\PasswordReset;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{

    /****************************************************************************************************
     * Get user main credential (email / mobile)
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    private static function getMainCredential(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:128'
        ]);

        $field = Credential::field($request->username);
        $credentials = User::select('email', 'mobile', 'username', 'first_name')->where($field, $request->username)->get();
        if(sizeof($credentials) <= 0) {
            throw ValidationException::withMessages([
                'username' => ['No user found for the given ' . $field . '.']
            ]);
        }
        else
            return $credentials[0];
    }


    /****************************************************************************************************
     * Check supplied e-mail / username / mobile
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function check_username(Request $request)
    {
        $credentials = self::getMainCredential($request);

        // partially hide credentials for viewing
        return response([
            'credentials' => [
                'email'  => Credential::mask($credentials['email'], 'email'),
                'mobile' => Credential::mask($credentials['mobile'], 'mobile')
            ]
        ]);
    }


    /****************************************************************************************************
     * Generate and send OTP on the preferred reset method
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function send_otp(Request $request)
    {
        $request->validate([
            'mode' => 'required|string|in:email,mobile'
        ]);

        $credentials = self::getMainCredential($request);

        // generate OTP
        $otp = null;

        if($request->mode == 'email') {
            if($credentials['email'] != '') {
                $otp = Otp::generate($credentials['username']);

                // email the otp
                if(isset($otp->token)) {
                    $details = [
                        'first_name' => $credentials['first_name'],
                        'otp'        => $otp->token
                    ];
                    $otp->identifier = $credentials['username'];
                    Mail::to($credentials['email'])->send(new ResetPasswordMail($details));
                }
            }
            else {
                throw ValidationException::withMessages([
                    'email' => ['This account has no e-mail address.']
                ]);
            }
        }
        else if($request->mode == 'mobile') {
            if($credentials['mobile'] != '') {
                $otp = Otp::generate($credentials['username']);

                // text the otp
                if(isset($otp->token)) {
                    if(app()->environment('production'))
                        $response = MySMSPH::otp($credentials['mobile'], $otp->token, 'resetting your password');
                    else
                        $response = MySMSPH::otp_fake($credentials['mobile'], $otp->token);

                    if(isset($response['errors']))
                        return response($response, 422);
                    else
                        $otp->identifier = $credentials['username'];
                }
            }
            else {
                throw ValidationException::withMessages([
                    'mobile' => ['This account has no mobile number.']
                ]);
            }
        }

        if(isset($otp->identifier)) {
            return response([
                'otp_sent' => true,
            ]);
        }
        else {
            return response([
                'message' => 'Maximum number of OTPs reached. Please try again in ' . env('OTP_DELETE_TIME') . ' minutes after you first saw this message.'
            ]);
        }
    }


    /****************************************************************************************************
     * Verify OTP and create reset password token
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function verify_otp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|max:' . env('OPT_LENGTH'),
        ]);

        $credentials = self::getMainCredential($request);

        // generate password reset token
        $token = null;

        // verify otp
        $verify = Otp::validate($credentials['username'], $request->otp);

        if(isset($verify->status)) {
            if($verify->status) {
                $current_time = time();
                $token = $current_time . '-' . Str::random(8) . '-' . Str::random(4) . Str::random(8) . '-' . Str::random(4);

                PasswordReset::create([
                    'username'   => $credentials['username'],
                    'token'      => $token,
                    'created_at' => date('Y-m-d H:i:s', $current_time),
                    'expires_at' => date('Y-m-d H:i:s', $current_time + intval(env('RESET_TOKEN_VALIDITY_SECONDS')))
                ]);
            }
            else {
                return response([
                    'message' => $verify->message . '.' . ($verify->message == 'Reached the maximum allowed attempts' ? ' Please try again in ' . env('OTP_DELETE_TIME') . ' minutes after you first saw this message.' : '')
                ]);
            }
        }

        if($token != null) {
            return response([
                'token' => $token,
            ]);
        }
        else {
            return response([
                'message' => 'Unable to generate token. Please try again.'
            ]);
        }
    }


    /****************************************************************************************************
     * Handle the changing of user password
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function change_password(Request $request)
    {
        $request->validate([
            'otp'      => 'required|string|max:' . env('OPT_LENGTH'),
            'token'    => 'required|string',
            'password' => 'required|string|min:' . env('PASSWORD_MIN_CHARS') . '|confirmed',
        ]);

        $credentials = self::getMainCredential($request);

        $reset_details = PasswordReset::where([
            ['username'  , $credentials['username']],
            ['token'     , $request->token],
            ['completed' , 0],
            ['expires_at', '>=', date('Y-m-d H:i:s', time())]
        ])->first();

        if($reset_details) {
            $user = User::where('username', $credentials['username'])->first();
            if($user) {
                // update user password
                $user->update([
                    'password' => Hash::make($request->password)
                ]);

                // delete password reset token
                $reset_details->delete();

                // delete used otp
                $used_otp = OtpModel::where([
                    ['identifier', $credentials['username']],
                    ['token'     , $request->otp]
                ])->delete();

                // login the user
                Auth::login($user);
                $request->session()->regenerate();

                return response([
                    'password_changed' => true,
                ]);
            }
            else {
                return response([
                    'message' => 'User not found. Please try again.'
                ]);
            }
        }
        else {
            return response([
                'message' => 'Reset token is invalid or expired. Please try again.'
            ]);
        }
    }
}
