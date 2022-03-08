<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class VerifyUsername
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
            $requested_username = $request->header('Username');
            $user = User::where('username', $requested_username)->first();
            if($user === null) {
                return response('', 404)->withHeaders([
                    'invalid_username' => $requested_username
                ]);
            }
            else {
                $request->merge(['mddlwr_user' => $user]);
                return $next($request);
            }
        }
    }
}
