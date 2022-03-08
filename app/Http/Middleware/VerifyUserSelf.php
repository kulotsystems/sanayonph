<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyUserSelf
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!$request->hasHeader('Username')) {
            return response('', 400)->withHeaders([
                'invalid_username' => null
            ]);
        }
        else {
            $user = Auth::user();
            if($user == null) {
                return response('', 404)->withHeaders([
                    'invalid_username' => null
                ]);
            }
            else {
                $username = $user->username;
                if ($username !== $request->header('Username')) {
                    return response('', 403)->withHeaders([
                        'correct_username' => $username,
                        'wrong_username'   => $request->header('Username')
                    ]);
                }
                else
                    return $next($request);
            }
        }
    }
}
