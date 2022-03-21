<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ExploreController extends Controller
{
    /****************************************************************************************************
     * Perform search
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        $request->validate([
            'query' => 'string|max:255|nullable'
        ]);
        $q = $request['query'];


        // search products
        $products = [];
        if($q != null) {
            $result = Product::query()
                ->where('is_published', 1)
                ->where('name' , 'like', '%'.$q.'%')
                ->orWhere('description' , 'like', '%'.$q.'%')
                ->get();

            foreach ($result as $product) {
                $category = $product->category;
                $store    = $category->store;
                $store->makeHidden('user');

                $product->category;
                $product->makeVisible('category');
                $product['username'] = $product->category->store->user->username;
                array_push($products, $product);
            }
        }

        // search stores
        $stores = [];
        if($q != null) {
            $result = Store::published()
                ->whereHas('user', function($query) use ($q) {
                    $query
                        ->where('first_name'   , 'like', '%'.$q.'%')
                        ->orWhere('middle_name', 'like', '%'.$q.'%')
                        ->orWhere('last_name'  , 'like', '%'.$q.'%')
                        ->orWhere('username'   , 'like', '%'.$q.'%');
                })
                ->orWhere('name' , 'like', '%'.$q.'%')
                ->orWhere('description' , 'like', '%'.$q.'%')
                ->get();
            foreach ($result as $store) {
                array_push($stores, [
                    'store'   => $store->only('id', 'slug'),
                    'user'    => $store->user->seller_info(),
                    'address' => $store->user->delivery_addresses->first()->muncity_address()
                ]);
            }
        }

        return response([
            'products' => $products,
            'stores'   => $stores
        ]);
    }
}
