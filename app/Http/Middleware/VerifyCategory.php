<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyCategory
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
        if(!$request->hasHeader('Category')) {
            return response('', 400)->withHeaders([
                'invalid_category' => null
            ]);
        }
        else {
            $requested_category = $request->header('Category');
            $category = $request->mddlwr_store->categories->where('id', $requested_category)->first();
            if($category === null) {
                return response('', 404)->withHeaders([
                    'invalid_category' => $requested_category
                ]);
            }
            else {
                $request->merge(['mddlwr_category' => $category]);
                return $next($request);
            }
        }
    }
}
