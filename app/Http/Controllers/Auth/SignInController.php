<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Helpers\Credential;

class SignInController extends Controller
{

    /****************************************************************************************************
     * Handle user sign in
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function sign_in(Request $request)
    {
        $field = Credential::field($request->username);

        $credentials = [
            $field     => $request['username'],
            'password' => $request['password']
        ];

        if(Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response([
                'signed_in' => true
            ]);
        }

        throw ValidationException::withMessages([
            'username' => ['Invalid ' . $field . ' or password']
        ]);
    }


    /****************************************************************************************************
     * Get signed in user
     *
     * @param Request $request
     * @return Response
     */
    public function get_user(Request $request)
    {
        $user = $request->user();
        if($user) {
            $user->makeVisible('gcash_name');
            $user->makeVisible('gcash_number');
        }
        return response([
            'user' => $user
        ]);
    }
}
