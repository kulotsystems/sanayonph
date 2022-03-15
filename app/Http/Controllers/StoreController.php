<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class StoreController extends Controller
{

    /****************************************************************************************************
     * Get all stores
     *
     * @param Request $request
     * @return Response
     */
    public function stores(Request $request)
    {
        $stores = [];
        foreach (Store::published()->get() as $store) {
            array_push($stores, [
                'store'   => $store->only('id', 'slug'),
                'user'    => $store->user->seller_info(),
                'address' => $store->user->delivery_addresses->first()->muncity_address()
            ]);
        }

        return response([
            'stores' => $stores
        ]);
    }


    /****************************************************************************************************
     * Get user associated with store
     *
     * @param Request $request
     * @return Response
     */
    public function user(Request $request)
    {
        $user = $request->mddlwr_user;
        return response([
            'user' => [
                'avatar'      => $user->avatar,
                'name'        => $user->name,
                'store'       => $user->store,
                'username'    => $user->username,
                'first_name'  => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name'   => $user->last_name,
                'name_suffix' => $user->name_suffix,
            ]
        ]);
    }


    /****************************************************************************************************
     * Get products from store
     *
     * @param Request $request
     * @return Response
     */
    public function products(Request $request)
    {
        $store = $request->mddlwr_store;

        $categories = [];
        foreach ($store->categories as $category) {
            if(sizeof($category->published_products) > 0) {
                $category->makeVisible('published_products');
                array_push($categories, $category);
            }
        }
        return response([
            'categories' => $categories
        ]);
    }


    /****************************************************************************************************
     * Get product details
     *
     * @param Request $request
     * @param $id
     * @param $action  'display' | 'buy'
     * @return Response
     */
    public function product(Request $request, $id, $action)
    {
        return response(Store::get_product_details($request->mddlwr_product, $action));
    }


    /****************************************************************************************************
     * Generate and get merchant code for store verification
     *
     * @param Request $request
     * @return Response
     */
    public function merchant_code(Request $request)
    {
        $user  = $request->mddlwr_user;
        $store = $user->store;
        if($store->merchant_code == null) {
            $store->update([
                'merchant_code' => Str::random()
            ]);
        }
        return response([
            'code' => $store->merchant_code
        ]);
    }
}
