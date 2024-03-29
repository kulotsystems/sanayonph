<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Models\Sale;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class OrderController extends Controller
{

    /****************************************************************************************************
     * Display a listing of user's orders.
     *
     * @param Request $request
     * @return Response
     */
    public function buyer_index(Request $request)
    {
        $user   = $request->mddlwr_user;
        $orders = [];
        foreach ($user->orders as $order) {
            if($order->sales->count() > 0) {
                array_push($orders, [
                    'id'                   => $order->id,
                    'ordered_at_str'       => $order->ordered_at_str,
                    'seller'               => $order->seller,
                    'total'                => $order->total,
                    'delivery_method_name' => $order->delivery_method->name,
                    'payment_method_name'  => $order->payment_method->name,
                    'order_status'         => $order->status['order']['status'],
                    'sale'                 => $order->sales[0]->only(
                        'product_name',
                        'product_image',
                        'product_label',
                        'quantity',
                        'category_name',
                        'price_after_quantity'
                    )
                ]);
            }
        }
        return response([
            'orders' => $orders
        ]);
    }


    /****************************************************************************************************
     * Display a listing of orders from seller
     *
     * @param Request $request
     * @return Response
     */
    public function seller_index(Request $request)
    {
        $store  = $request->mddlwr_store;
        $orders = [];
        foreach ($store->orders as $order) {
            if($order->sales->count() > 0) {
                array_push($orders, [
                    'id'                   => $order->id,
                    'ordered_at_str'       => $order->ordered_at_str,
                    'buyer'                => $order->buyer,
                    'total'                => $order->total,
                    'delivery_method_name' => $order->delivery_method->name,
                    'payment_method_name'  => $order->payment_method->name,
                    'order_status'         => $order->status['order']['status'],
                    'sale'                 => $order->sales[0]->only(
                        'product_name',
                        'product_image',
                        'product_label',
                        'quantity',
                        'category_name',
                        'price_after_quantity'
                    )
                ]);
            }
        }
        return response([
            'orders' => $orders
        ]);
    }


    /****************************************************************************************************
     *  Place order.
     *
     * @param Request $request
     * @param $id
     * @return Response
     * @throws ValidationException
     */
    public function place_order(Request $request, $id)
    {
        $request->validate([
            'var1_index'       => 'numeric|required',
            'var2_index'       => 'numeric|required',
            'quantity'         => 'numeric|required',
            'delivery_method'  => 'numeric|required',
            'delivery_address' => 'numeric|required',
            'payment_method'   => 'numeric|required',
            'final_price'      => 'numeric|required'
        ]);

        // compute placed order
        $placed_order = Store::compute_order($request, Store::get_product_details($request->mddlwr_product, 'buy'));

        // store order
        $user  = Auth::user();
        $store = $request->mddlwr_store;
        $order = new Order();
        $order->user_id             = $user->id;
        $order->store_id            = $store->id;
        $order->delivery_method_id  = $placed_order['request']['delivery_method'];
        $order->payment_method_id   = $placed_order['request']['payment_method'];
        $order->delivery_address_id = $placed_order['computed']['finalDeliveryAddressID'];
        $order->delivery_address    = $placed_order['computed']['finalDeliveryAddress'];
        $order->delivery_fullname   = $user->name['full_name_1'];
        $order->delivery_mobile     = $user->mobile;
        $order->delivery_email      = $user->email;
        $order->ordered_at          = now();
        $order->save();

        // store sale
        $sale = new Sale();
        $sale->order_id                    = $order->id;
        $sale->product_id                  = $placed_order['config']['product']['id'];
        $sale->product_name                = $placed_order['config']['product']['name'];
        $sale->product_description         = $placed_order['config']['product']['description'];
        $sale->product_published_at        = $placed_order['config']['product']['published_at'];
        $sale->product_image               = $placed_order['computed']['productOverview']['image'];
        $sale->product_price               = $placed_order['computed']['productOverview']['price'];
        $sale->product_stock               = $placed_order['computed']['productOverview']['stock'];
        $sale->product_label               = $placed_order['computed']['productOverview']['label'];
        $sale->category_name               = $placed_order['config']['product']['category']['name'];
        $sale->service_fee_percentage      = $placed_order['computed']['serviceFeeAfterShipping']['percent'];
        $sale->quantity                    = $placed_order['request']['quantity'];
        $sale->price_after_quantity        = $placed_order['computed']['priceAfterQuantity'];
        $sale->shipping_fee_after_quantity = $placed_order['computed']['shippingFeeAfterQuantity']['amount'];
        $sale->price_after_shipping        = $placed_order['computed']['priceAfterShipping'];
        $sale->service_fee_after_shipping  = $placed_order['computed']['serviceFeeAfterShipping']['amount'];
        $sale->price_after_service_fee     = $placed_order['computed']['priceAfterServiceFee'];
        $sale->save();

        // decrease stock
        $order->update_stock('decrease');

        return response([
            'ordered' =>  $order->id
        ]);
    }


    /****************************************************************************************************
     * Fetch data for showing an order.
     *
     * @param Request $request
     * @param $id
     * @return Response
     */

    public function buyer_show(Request $request, $id)
    {
        $order = $request->mddlwr_order;
        return response([
            'order' => $order->details_for('buyer')
        ]);
    }


    /****************************************************************************************************
     * Cancel order by buyer
     *
     * @param Request $request
     * @param $id
     * @return Response
     * @throws ValidationException
     */
    public function buyer_cancel(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'string|required|max:300'
        ]);

        $order = $request->mddlwr_order;

        if($order->status['order']['status'] == Order::$ORDER_CANCELLED_BY_BUYER) {
            throw ValidationException::withMessages([
                'payment' => ['Order is already cancelled.']
            ]);
        }
        else if($order->status['order']['status'] == Order::$ORDER_DECLINED_BY_SELLER) {
            throw ValidationException::withMessages([
                'payment' => ['Order is already declined.']
            ]);
        }
        else if($order->status['payment']['status'] == Order::$PAYMENT_DECLINED) {
            throw ValidationException::withMessages([
                'payment' => ['Current payment is declined.']
            ]);
        }
        else if($order->status['payment']['status'] == Order::$PAYMENT_CONFIRMED) {
            throw ValidationException::withMessages([
                'payment' => ['Payment is already confirmed.']
            ]);
        }
        else {
            // update order
            $order->update([
                'cancelled_by_buyer_at' => now(),
                'reason_by_buyer'       => $request->remarks
            ]);

            // increase stock
            $order->update_stock('increase');

            return response([
                'status' => $order->status
            ]);
        }
    }


    /****************************************************************************************************
     * Receive order by buyer
     *
     * @param Request $request
     * @param $id
     * @return Response
     * @throws ValidationException
     */
    public function buyer_receive(Request $request, $id)
    {
        $order = $request->mddlwr_order;

        if($order->status['delivery']['status'] != Order::$DELIVERY_FOR_PICKUP) {
            throw ValidationException::withMessages([
                'delivery' => ['Order is not yet available for pickup or may have already been received.']
            ]);
        }
        else {
            // update order
            $order->update([
                'received_at' => now()
            ]);

            return response([
                'status' => $order->status
            ]);
        }
    }


    /****************************************************************************************************
     * Review order by buyer
     *
     * @param Request $request
     * @param $id
     * @return Response
     * @throws ValidationException
     */
    public function buyer_review(Request $request, $id)
    {
        $request->validate([
            'reviews' => 'required|array'
        ]);

        $user  = $request->mddlwr_user;
        $order = $request->mddlwr_order;
        for($i=0; $i<$order->sales->count(); $i++) {
            $rating  = $request->reviews[$i]['rating'];
            $content = trim($request->reviews[$i]['content']);
            if($rating <= 0 || $rating > 5) {
                throw ValidationException::withMessages([
                    'rating['.($i+1).']' => ['Review rating should not be less than or equal to 0 or greater than 5.']
                ]);
            }
            else if($content == '') {
                throw ValidationException::withMessages([
                    'content['.($i+1).']' => ['Review content should not be empty.']
                ]);
            }
            else {
                $sale = $order->sales[$i];
                if($sale->review == null) {
                    // create review
                    $review = new Review();
                    $review->sale_id = $sale->id;
                    $review->rating  = $rating;
                    $review->content = substr($content, 0, 300);
                    $review->save();
                }
                else {
                    // update review
                    $sale->review->rating  = $rating;
                    $sale->review->content = substr($content, 0, 300);
                    $sale->review->update();
                }
            }
        }
        return response([
            'reviewed' => true
        ]);
    }


    /****************************************************************************************************
     * Upload payment screenshot
     *
     * @param Request $request
     * @param $id
     * @return Response
     * @throws ValidationException
     */
    public function buyer_screenshot(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user  = $request->mddlwr_user;
        $order = $request->mddlwr_order;

        if(!$order->payment_method->is_gcash) {
            throw ValidationException::withMessages([
                'payment_method' => ['Payment is not through Gcash.']
            ]);
        }
        else if($order->status['payment']['status'] == Order::$PAYMENT_DECLINED) {
            throw ValidationException::withMessages([
                'payment' => ['Current payment is declined.']
            ]);
        }
        else if($order->status['payment']['status'] == Order::$PAYMENT_CONFIRMED) {
            throw ValidationException::withMessages([
                'payment' => ['Payment is already confirmed.']
            ]);
        }
        else if($order->status['order']['status'] == Order::$ORDER_DECLINED_BY_SELLER) {
            throw ValidationException::withMessages([
                'order' => ['Order is declined by seller.']
            ]);
        }
        else if($order->status['order']['status'] == Order::$ORDER_CANCELLED_BY_BUYER) {
            throw ValidationException::withMessages([
                'order' => ['Order is cancelled by buyer.']
            ]);
        }
        else {

            // extract image from request
            $image = $request->file('file');

            // upload the image on /public/uploads/images/screenshots
            $file_path = $image->storePubliclyAs('images/screenshots/', 'prod-'.$user->id.'-'.$order->id.'-'. Str::random(8) .'-'.time().'-'. Str::random(4) .'.png', ['disk' => 'uploads']);

            // update order screenshot
            $file_name = basename($file_path);
            $order->update([
                'payment_screenshot'       => $file_name,
                'payment_screenshot_at'    => now(),
                'payment_declined_at'      => null,
                'payment_declined_remarks' => null
            ]);

            // return response
            return response([
                'payment_status' => $order->status['payment']
            ]);
        }
    }


    /****************************************************************************************************
     * Fetch data for showing an order in a store
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function seller_show(Request $request, $id)
    {
        $order = $request->mddlwr_order;
        return response([
            'order' => $order->details_for('seller')
        ]);
    }


    /****************************************************************************************************
     * Decline order by seller
     *
     * @param Request $request
     * @param $id
     * @return Response
     * @throws ValidationException
     */
    public function seller_decline(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'string|required|max:300'
        ]);

        $order = $request->mddlwr_order;

        if($order->status['order']['status'] == Order::$ORDER_CANCELLED_BY_BUYER) {
            throw ValidationException::withMessages([
                'payment' => ['Order is already cancelled.']
            ]);
        }
        else if($order->status['order']['status'] == Order::$ORDER_DECLINED_BY_SELLER) {
            throw ValidationException::withMessages([
                'payment' => ['Order is already declined.']
            ]);
        }
        else if($order->status['payment']['status'] == Order::$PAYMENT_DECLINED) {
            throw ValidationException::withMessages([
                'payment' => ['Buyer\'s payment is declined.']
            ]);
        }
        else if($order->status['payment']['status'] == Order::$PAYMENT_CONFIRMED) {
            throw ValidationException::withMessages([
                'payment' => ['Payment is already confirmed.']
            ]);
        }
        else if($order->status['order']['status'] == Order::$ORDER_CANCELLED_BY_BUYER) {
            throw ValidationException::withMessages([
                'order' => ['Order is cancelled by buyer.']
            ]);
        }
        else {
            // update order
            $order->update([
                'declined_by_seller_at' => now(),
                'reason_by_seller'      => $request->remarks
            ]);

            // increase stock
            $order->update_stock('increase');

            return response([
                'status' => $order->status
            ]);
        }
    }


    /****************************************************************************************************
     * Confirm order by seller
     *
     * @param Request $request
     * @param $id
     * @return Response
     * @throws ValidationException
     */
    public function seller_confirm(Request $request, $id)
    {
        $request->validate([
            'pickup_location' => 'string|required|max:300',
            'pickup_date'     => 'date|required',
            'pickup_time'     => 'date_format:H:i|required'
        ]);

        $order = $request->mddlwr_order;

        if($order->status['payment']['status'] == Order::$ORDER_FOR_PAYMENT) {
            throw ValidationException::withMessages([
                'payment' => ['Payment is not yet confirmed.']
            ]);
        }
        else if($order->status['order']['status'] == Order::$ORDER_DECLINED_BY_SELLER) {
            throw ValidationException::withMessages([
                'order' => ['Order is already declined.']
            ]);
        }
        else if($order->status['order']['status'] == Order::$ORDER_CANCELLED_BY_BUYER) {
            throw ValidationException::withMessages([
                'order' => ['Order is cancelled by buyer.']
            ]);
        }
        else {
            $pickup_datetime = Carbon::createFromFormat('Y-m-d H:i', $request->pickup_date . ' ' . $request->pickup_time);
            if($pickup_datetime->lt(Carbon::now())) {
                throw ValidationException::withMessages([
                    'date_time' => ['Pickup date and time must be greater than current date and time.']
                ]);
            }

            // update order
            $order->update([
                'confirmed_by_seller_at' => now(),
                'pickup_location' => Str::upper($request->pickup_location),
                'pickup_at'       => $pickup_datetime
            ]);

            return response([
                'status' => $order->status
            ]);
        }
    }
}
