<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyDeliveryAddress
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
        if(!$request->hasHeader('DeliveryAddress')) {
            return response('', 400)->withHeaders([
                'invalid_delivery_address' => null
            ]);
        }
        else {
            $requested_delivery_address = $request->header('DeliveryAddress');
            $delivery_address = $request->mddlwr_user->delivery_addresses->where('id', $requested_delivery_address)->first();
            if($delivery_address === null) {
                return response('', 404)->withHeaders([
                    'invalid_delivery_address' => $requested_delivery_address
                ]);
            }
            else {
                $request->merge(['mddlwr_delivery_address' => $delivery_address]);
                return $next($request);
            }
        }
    }
}
