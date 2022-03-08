<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Product;
use App\Models\GeneralImage;
use App\Models\Variation;
use App\Models\Item;
use App\Models\PriceStock;
use Image;


class ProductController extends Controller
{
    /****************************************************************************************************
     * Validate product request
     *
     * @param Request $request
     */
    private static function validate_product(Request $request)
    {
        $request->validate([
            'category'           => 'integer|required',
            'name'               => 'string|required|max:50',
            'description'        => 'string|max:2000|nullable',
            'delivery_origin'    => 'integer|required',
            'allows_pickup'      => 'boolean|required',
            'images'             => 'array|required',
            'variations.0.title' => 'string|required|max:50',
            'variations.1.title' => 'string|required|max:50',
            'variations.0.items' => 'array|nullable',
            'variations.1.items' => 'array|nullable',
            'price_stock_mode'   => 'string|required|in:' . implode(',', Product::$PRICE_STOCK_MODES),
            'price'              => 'array|required',
            'stock'              => 'array|required',
        ]);
    }


    /****************************************************************************************************
     * Display a listing of store's products
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $store = $request->mddlwr_store;
        foreach ($store->categories as $category) {
            $category->products;
            $category->makeVisible('products');
        }
        return response([
            'categories' => $store->categories
        ]);
    }


    /****************************************************************************************************
     *  Store a newly created product.
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        // validate request
        self::validate_product($request);

        // validate delivery origin
        $user = $request->mddlwr_user;
        if($user->delivery_addresses->find($request->delivery_origin) == null) {
            throw ValidationException::withMessages([
                'delivery_origin' => ['Invalid delivery origin.']
            ]);
        }
        else {
            // continue store
            $store = $request->mddlwr_store;
            if($category = $store->categories->find($request->category)) {

                // store product
                $product = new Product();
                $product->category_id      = $request->category;
                $product->name             = $request->name;
                $product->description      = $request->description;
                $product->price_stock_mode = $request->price_stock_mode;
                $product->delivery_origin  = $request->delivery_origin;
                $product->allows_pickup    = $request->allows_pickup;

                $product->save();

                // store general images

                for($i=0; $i<sizeof($request->images); $i++) {
                    if($request->images[$i]['image'] != null) {
                        $general_image = new GeneralImage();
                        $general_image->product_id = $product->id;
                        $general_image->image      = $request->images[$i]['image'];
                        $general_image->save();
                    }
                }

                // store variations
                for($i=0; $i<sizeof($request->variations); $i++) {
                    $variation = new Variation();
                    $variation->product_id = $product->id;
                    $variation->index      = $i;
                    $variation->title      = Str::upper($request->variations[$i]['title']);
                    $variation->save();

                    // store variation items
                    for($j=0; $j<sizeof($request->variations[$i]['items']); $j++) {
                        $item = new Item();
                        $item->variation_id = $variation->id;
                        $item->index = $j;
                        $item->image = $request->variations[$i]['items'][$j]['image'];
                        $item->label = Str::replace(',', '-', Str::upper($request->variations[$i]['items'][$j]['label']));
                        $item->save();
                    }
                }

                // store stock and price
                foreach(Product::$PRICE_STOCK_MODES as $price_stock_mode) {

                    // only entertain selected price_stock_mode
                    if($price_stock_mode == $product->price_stock_mode) {
                        foreach ($request->price[$price_stock_mode] as $indices => $price) {
                            $arr_indices = explode('_', $indices);
                            $var1_index = 0;
                            $var2_index = 0;
                            if(sizeof($arr_indices) == 1) {
                                // var1_only or var2_only
                                $var2_index = 0;
                                if($product->price_stock_mode == 'var1_only')
                                    $var1_index = $arr_indices[0];
                                else
                                    $var2_index = $arr_indices[0];
                            }
                            else {
                                // both_vars
                                $var1_index = $arr_indices[0];
                                $var2_index = $arr_indices[1];
                            }
                            $price_stock = new PriceStock();
                            $price_stock->product_id       = $product->id;
                            $price_stock->price_stock_mode = $price_stock_mode;
                            $price_stock->var1_item_index  = $var1_index;
                            $price_stock->var2_item_index  = $var2_index;
                            $price_stock->price = intval($price);
                            $price_stock->stock = isset($request->stock[$price_stock_mode][$indices]) ? intval($request->stock[$price_stock_mode][$indices]) : 0;
                            $price_stock->save();
                        }
                    }
                }

                return response([
                    'stored' => true
                ]);
            }
            else {
                throw ValidationException::withMessages([
                    'category' => ['The category does not exist in the store.']
                ]);
            }
        }
    }


    /****************************************************************************************************
     *  Fetch data for showing a product.
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request)
    {
        $product = $request->mddlwr_product;
        $product->elaborate();
        $product->makeVisible('category');
        $product->makeVisible('variations');
        $product->makeVisible('prices_stocks');

        return response([
            'product' => $product
        ]);
    }


    /****************************************************************************************************
     * Fetch data for editing a product.
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $user    = $request->mddlwr_user;
        $product = $request->mddlwr_product;
        $product->elaborate();
        $product->makeVisible('category_id');
        $product->makeVisible('variations');
        $product->makeVisible('prices_stocks');

        return response([
            'product'          => $product,
            'categories'       => $product->category->store->categories,
            'delivery_origins' => $user->plain_delivery_addresses()
        ]);
    }


    /****************************************************************************************************
     * Update the specified product.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        // validate request
        self::validate_product($request);

        // validate delivery origin
        $user = $request->mddlwr_user;
        if($user->delivery_addresses->find($request->delivery_origin) == null) {
            throw ValidationException::withMessages([
                'delivery_origin' => ['Invalid delivery origin.']
            ]);
        }
        else {

            // continue update
            $product = $request->mddlwr_product;

            // update product
            $product->category_id      = $request->category;
            $product->name             = $request->name;
            $product->description      = $request->description;
            $product->price_stock_mode = $request->price_stock_mode;
            $product->delivery_origin  = $request->delivery_origin;
            $product->allows_pickup    = $request->allows_pickup;

            $product->update();

            // update general images
            $prev_gen_images  = $product->gen_images;
            $new_images_total = 0;
            for($i=0; $i<sizeof($request->images); $i++) {
                if ($request->images[$i]['image'] != null) {
                    if($i < sizeof($prev_gen_images)) {
                        // update image
                        $general_image = $product->general_images[$i];
                        $general_image->image = $request->images[$i]['image'];
                        $general_image->update();
                    }
                    else {
                        // add image
                        $general_image = new GeneralImage();
                        $general_image->product_id = $product->id;
                        $general_image->image = $request->images[$i]['image'];
                        $general_image->save();
                    }
                    $new_images_total += 1;
                }
            }

            // remove not-included general images
            $old_images_total = sizeof($prev_gen_images);
            $stripped_total   =  $old_images_total - $new_images_total;
            for($i=1; $i<=$stripped_total; $i++) {
                $index = $old_images_total - $i;
                $product->general_images->where('image', $prev_gen_images[$index])->first()->delete();
            }

            // update variations
            for($i=0; $i<sizeof($request->variations); $i++) {
                $variation = $product->variations->where('index', $i)->first();
                $variation->title = Str::upper($request->variations[$i]['title']);
                $variation->update();

                // update variation items
                for($j=0; $j<sizeof($request->variations[$i]['items']); $j++) {
                    $item = $variation->items->where('index', $j)->first();
                    if($item) {
                        // update item
                        $item->image = $request->variations[$i]['items'][$j]['image'];
                        $item->label = Str::replace(',', '-', Str::upper($request->variations[$i]['items'][$j]['label']));
                        $item->update();
                    }
                    else {
                        // add item
                        $item = new Item();
                        $item->variation_id = $variation->id;
                        $item->index = $j;
                        $item->image = $request->variations[$i]['items'][$j]['image'];
                        $item->label = Str::replace(',', '-', Str::upper($request->variations[$i]['items'][$j]['label']));
                        $item->save();
                    }
                }

                // remove not-included variation items
                $old_items_total = count($variation->items->all());
                $new_items_total = sizeof($request->variations[$i]['items']);
                $stripped_total  = $old_items_total - $new_items_total;
                for($j=1; $j<=$stripped_total; $j++) {
                    $index = $old_items_total - $j;
                    $variation->items->where('index', $index)->first()->delete();

                    // remove related prices_stocks
                    foreach($product->prices_stocks->where('var'.($i+1).'_item_index', $index)->all() as $price_stock) {
                        $price_stock->delete();
                    }
                }
            }

            // update prices_stocks
            foreach(Product::$PRICE_STOCK_MODES as $price_stock_mode) {
                // only entertain selected price_stock_mode
                if($price_stock_mode == $product->price_stock_mode) {
                    foreach ($request->price[$price_stock_mode] as $indices => $price) {
                        $arr_indices = explode('_', $indices);
                        $var1_index = 0;
                        $var2_index = 0;
                        if(sizeof($arr_indices) == 1) {
                            // var1_only or var2_only
                            $var2_index = 0;
                            if($product->price_stock_mode == 'var1_only')
                                $var1_index = $arr_indices[0];
                            else
                                $var2_index = $arr_indices[0];
                        }
                        else {
                            // both_vars
                            $var1_index = $arr_indices[0];
                            $var2_index = $arr_indices[1];
                        }

                        $price_stock = $product->prices_stocks
                            ->where('price_stock_mode', $product->price_stock_mode)
                            ->where('var1_item_index' , $var1_index)
                            ->where('var2_item_index' , $var2_index)
                            ->first();
                        if($price_stock) {
                            // update price_stock
                            $price_stock->price = intval($price);
                            $price_stock->stock = isset($request->stock[$price_stock_mode][$indices]) ? intval($request->stock[$price_stock_mode][$indices]) : 0;
                            $price_stock->update();
                        }
                        else {
                            // add price_stock
                            $price_stock = new PriceStock();
                            $price_stock->product_id       = $product->id;
                            $price_stock->price_stock_mode = $price_stock_mode;
                            $price_stock->var1_item_index  = $var1_index;
                            $price_stock->var2_item_index  = $var2_index;
                            $price_stock->price = intval($price);
                            $price_stock->stock = isset($request->stock[$price_stock_mode][$indices]) ? intval($request->stock[$price_stock_mode][$indices]) : 0;
                            $price_stock->save();
                        }
                    }
                }
            }

            // delete prices_stocks with different price_stock_mode
            foreach (Product::$PRICE_STOCK_MODES as $price_stock_mode) {
                if($price_stock_mode != $product->price_stock_mode) {
                    foreach(PriceStock::where('product_id', $product->id)->where('price_stock_mode', $price_stock_mode)->get() as $price_stock) {
                        $price_stock->delete();
                    }
                }
            }

            return response([
                'updated' => true
            ]);
        }
    }


    /****************************************************************************************************
     * Remove the specified product.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function destroy(Request $request, $id)
    {
        $store   = $request->mddlwr_store;
        $product = null;
        foreach ($store->categories as $category) {
            if($product = $category->products->where('id', $id)->first()) {
                break;
            }
        }
        if($product != null) {
            $product->delete();
            return response([
                'deleted' => true
            ]);
        }
        else {
            throw ValidationException::withMessages([
                'product' => ['Product not found.']
            ]);
        }
    }


    /****************************************************************************************************
     * Upload product photo
     *
     * @param Request $request
     * @return Response
     */
    public function upload_photo(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // extract image from request
        $image = $request->file('file');

        // reference the user
        $user = Auth::user();

        // upload the image on /public/uploads/images/products/300
        $file_path = $image->storePubliclyAs('images/products/300', 'prod-'.$user->id.'-'. Str::random(8) .'-'.time().'-'. Str::random(4) .'.png', ['disk' => 'uploads']);

        $file_name = basename($file_path);

        // make a thumbnail of the image
        $thumbnail = Image::make($image->getRealPath());

        // save 128 x 128 thumbnail
        $thumbnail->resize(128, 128, function($constraint) {
            $constraint->aspectRatio();
        })->save(public_path('uploads/images/products/128/') . $file_name);

        // save 64 x 64 thumbnail
        $thumbnail->resize(64, 64, function($constraint) {
            $constraint->aspectRatio();
        })->save(public_path('uploads/images/products/064/') . $file_name);

        // return response
        return response([
            'file_name' => $file_name
        ]);
    }


    /****************************************************************************************************
     * Publish or unpublish product
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function publish(Request $request, $id)
    {
        $request->validate([
            'is_publishing' => 'boolean|required'
        ]);

        $store   = $request->mddlwr_store;
        $product = null;
        foreach ($store->categories as $category) {
            if ($product = $category->products->where('id', $id)->first()) {
                break;
            }
        }
        if($product != null) {
            if($product->category->store->id == $store->id) {
                $product->is_published = $request->is_publishing;
                if($request->is_publishing)
                    $product->published_at = now();
                $product->update();

                return response([
                    'published' => $product->published_at
                ]);
            }
        }
        else {
            throw ValidationException::withMessages([
                'product' => ['Product not found.']
            ]);
        }
    }


    /****************************************************************************************************
     * Get dependencies
     *
     * @param Request $request
     * @return Response
     */
    public function dependencies(Request $request)
    {
        $user  = $request->mddlwr_user;
        $store = $request->mddlwr_store;
        return response([
            'categories'       => $store->categories,
            'delivery_origins' => $user->plain_delivery_addresses()
        ]);
    }

}
