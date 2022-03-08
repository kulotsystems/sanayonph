<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyProduct
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
        if(!$request->hasHeader('Product')) {
            return response('', 400)->withHeaders([
                'invalid_product' => null
            ]);
        }
        else {
            $requested_product = $request->header('Product');
            $product  = null;
            foreach ($request->mddlwr_store->categories as $category) {
                if($product = $category->products->where('id', $requested_product)->first()) {
                    break;
                }
            }
            if($product === null) {
                return response('', 404)->withHeaders([
                    'invalid_product' => $requested_product
                ]);
            }
            else {
                $request->merge(['mddlwr_product' => $product]);
                return $next($request);
            }
        }
    }
}
