<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyOrder
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
        if(!$request->hasHeader('Order')) {
            return response('', 400)->withHeaders([
                'invalid_order' => null
            ]);
        }
        else {
            $requested_order = $request->header('Order');
            $order = null;
            if(isset($request->mddlwr_store))
                $order = $request->mddlwr_store->orders->where('id', $requested_order)->first();
            else if(isset($request->mddlwr_user))
                $order = $request->mddlwr_user->orders->where('id', $requested_order)->first();

            if($order === null) {
                return response('', 404)->withHeaders([
                    'invalid_order' => $requested_order
                ]);
            }
            else {
                $request->merge(['mddlwr_order' => $order]);
                return $next($request);
            }
        }
    }
}
