<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SignUpMail;
use App\Helpers\MySMSPH;
use App\Models\User;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Seshac\Otp\Otp;
use App\Models\OtpModel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Credential;
use Illuminate\Support\Str;

class SignUpController extends Controller
{

    /****************************************************************************************************
     * Validate sign up data aside from email and mobile
     *
     * @param Request $request
     */
    private static function validate_sign_up(Request $request)
    {
        $request->validate([
            'email_or_mobile' => 'required|string|max:128',
            'first_name'      => 'required|string|max:64',
            'middle_name'     => 'string|max:64|nullable',
            'last_name'       => 'required|string|max:64',
            'name_suffix'     => 'string|max:32|nullable',
            'gender'          => 'required|string|in:Male,Female',
            'date_of_birth'   => 'required|date|before:-15 years',
            'username'        => 'required|alpha_dash|max:16|not-in:sign-up,reset-password,explore,xx-demo,shop|unique:snyn_users',
            'password'        => 'required|string|min:' . env('PASSWORD_MIN_CHARS') . '|confirmed'
        ]);
    }


    /****************************************************************************************************
     * Generate and send OTP on the preferred sign up method
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function send_otp(Request $request)
    {
        // validate fields
        self::validate_sign_up($request);

        // append mode field
        $request->request->add(['mode' => Credential::field($request->email_or_mobile)]);

        if(in_array($request->mode, ['email', 'mobile'])) {
            // generate OTP
            $otp = null;

            // if sign up method is through email,
            // validate the email, then generate and send OTP
            if($request->mode == 'email') {
                // append email field
                $request->request->add(['email' => $request->email_or_mobile]);

                // validate email
                $request->validate([
                    'email' => 'required|email|unique:snyn_users|max:128',
                ]);

                $otp = Otp::generate($request->email);

                // email the otp
                if(isset($otp->token)) {
                    $details = [
                        'first_name' => $request->first_name,
                        'otp'        => $otp->token
                    ];
                    $otp->identifier = $request->email;
                    Mail::to($request->email)->send(new SignUpMail($details));
                }
            }

            // if sign up method is through mobile,
            // validate the mobile number, then generate and send OTP
            else if($request->mode == 'mobile') {
                // append mobile number field
                $request->request->add(['mobile' => $request->email_or_mobile]);

                // validate mobile number
                $request->validate([
                    'mobile' => 'required|unique:snyn_users|regex:/(09)[0-9]{9}/'
                ]);

                $otp = Otp::generate($request->mobile);

                // text the otp
                if(isset($otp->token)) {
                    if(app()->environment('production'))
                        $response = MySMSPH::otp($request->mobile, $otp->token);
                    else
                       $response = MySMSPH::otp_fake($request->mobile, $otp->token);

                    if(isset($response['errors']))
                        return response($response, 422);
                    else
                        $otp->identifier = $request->mobile;
                }
            }
            if(isset($otp->identifier)) {
                return response([
                    'identifier' => $otp->identifier,
                ]);
            }
            else {
                return response([
                    'message' => 'Maximum number of OTPs reached. Please try again in ' . env('OTP_DELETE_TIME') . ' minutes after you first saw this message.'
                ]);
            }
        }
        else {
            throw ValidationException::withMessages([
                'email_or_mobile' => ['Invalid E-mail or Mobile Number']
            ]);
        }
    }


    /****************************************************************************************************
     * Complete the sign up process
     *
     * @param Request $request
     * @return Response
     */

    public function verify_otp(Request $request)
    {
        // validate fields
        self::validate_sign_up($request);

        // append mode field
        $request->request->add(['mode' => Credential::field($request->email_or_mobile)]);

        // validate identifier and otp
        $request->validate([
            'identifier' => 'required|string',
            'otp'        => 'required|string|max:' . env('OPT_LENGTH'),
        ]);

        // verify otp
        $verify = Otp::validate($request->identifier, $request->otp);

        if(isset($verify->status)) {
            if($verify->status) {

                // register new user
                $user = new User();
                $user->first_name    = Str::title($request->first_name);
                $user->middle_name   = Str::title($request->middle_name);
                $user->last_name     = Str::title($request->last_name);
                $user->name_suffix   = (Str::upper(Str::of($request->name_suffix)->substr(0, 1)) == 'I') ? Str::upper($request->name_suffix) : Str::title($request->name_suffix);
                $user->gender        = $request->gender == 'Male' ? 1 : 0;
                $user->date_of_birth = $request->date_of_birth;
                $user->username      = Str::lower($request->username);
                $user->password      = Hash::make($request->password);
                if($request->mode == 'email') {
                    $user->email = $request->email_or_mobile;
                    $user->email_verified_at = now();
                }
                else if($request->mode == 'mobile') {
                    $user->mobile = $request->email_or_mobile;
                    $user->mobile_verified_at = now();
                }
                $user->save();

                // delete used otp
                $used_otp = OtpModel::where([
                    ['identifier', $request[$request->mode]],
                    ['token'     , $request->otp]
                ])->delete();

                // auto-login
                Auth::login($user);

                // create user's default store
                $store = new Store();
                $store->user_id = $user->id;
                $store->save();

                return response([
                    'registered' => true,
                ]);
            }
            else {
                return response([
                    'message' => $verify->message . '.' . ($verify->message == 'Reached the maximum allowed attempts' ? ' Please try again in ' . env('OTP_DELETE_TIME') . ' minutes after you first saw this message.' : '')
                ]);
            }
        }
        else {
            return response([
                'message' => 'An unknown error has occurred. Please try again later.'
            ]);
        }
    }
}
