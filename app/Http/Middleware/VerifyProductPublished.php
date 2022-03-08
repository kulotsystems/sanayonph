<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyProductPublished
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
        if($request->mddlwr_product == null) {
            return response('', 404)->withHeaders([
                'invalid_product' => 404
            ]);
        }
        else {
            if(!$request->mddlwr_product->is_published) {
                return response('', 404)->withHeaders([
                    'invalid_product' => 'unpublished'
                ]);
            }
            else
                return $next($request);
        }
    }
}
