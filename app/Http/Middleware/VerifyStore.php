<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyStore
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
        if(!$request->hasHeader('Store')) {
            return response('', 400)->withHeaders([
                'invalid_store' => null
            ]);
        }
        else {
            $requested_store = $request->header('Store');
            $store = $request->mddlwr_user->stores->where('slug', $requested_store)->first();
            if($store === null) {
                return response('', 404)->withHeaders([
                    'invalid_store' => $requested_store
                ]);
            }
            else {
                $request->merge(['mddlwr_store' => $store]);
                return $next($request);
            }
        }
    }
}
