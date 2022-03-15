<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    /****************************************************************************************************
     * @param Request $request
     */
    public function index(Request $request){
        
        # Get all store first for testing
        $stores = [];
        $min_id = app()->environment('production') ? 3 : 1;
        foreach (Store::where('id', '>=', $min_id)->get() as $store) {
            $include = false;
            foreach ($store->categories as $category) {
                if($category->products->where('is_published', 1)->count() > 0) {
                    $include = true;
                    break;
                }
            }

            if($include) {
                array_push($stores, [
                    'store'   => $store->only('id', 'slug'),
                    'user'    => $store->user->seller_info(),
                    'address' => $store->user->delivery_addresses->first()->muncity_address()
                ]);
            }
        }

        # No products muna hehe...
        $products = [];

        return response([
            'stores'    => $stores,
            'products'  => $products
        ]);

    }
}
