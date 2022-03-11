<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Order;

class AgentController extends Controller
{
    private static $limit = 10;

    /****************************************************************************************************
     * Verify auth
     *
     * @param $auth
     * @return boolean
     */
    private static function valid_auth($auth)
    {
        return password_verify(env('AGENT_SECRET'), $auth);
    }


    /****************************************************************************************************
     * Pagination properties
     *
     * @param $mode
     * @param $offset
     * @param $total
     * @return array
     */
    private static function pagination($mode, $offset, $total)
    {
        if ($mode == 'prev')
            $offset -= self::$limit;
        else if ($mode == 'next')
            $offset += self::$limit;

        if ($offset >= $total && $total > 0)
            $offset = ($total - self::$limit) + ($offset % $total);
        if ($offset < 0)
            $offset = 0;

        $offset2 = $offset + self::$limit;
        if ($offset2 > $total) {
            $offset2 = $total;
            self::$limit = $total - $offset;
        }

        return [
            'offset'  => $offset,
            'offset2' => $offset2
        ];
    }


    /****************************************************************************************************
     * Get payments to confirm
     *
     * @param Request $request
     * @return Response
     */
    public function payments_to_confirm(Request $request)
    {
        // validate request
        $request->validate([
            'mode'   => 'string|required',
            'offset' => 'integer|required',
            'auth'   => 'string|required',
        ]);

        if(!self::valid_auth($request->auth))
            return response(['error' => 'ACCESS DENIED']);
        else {
            // get total orders to confirm payment
            $total = Order::select('id')
                ->where('user_id', '>=', app()->environment('production') ? 3 : 1)
                ->where('payment_method_id', 2)
                ->whereNotNull('payment_screenshot')
                ->whereNull('payment_confirmed_at')
                ->whereNull('payment_declined_at')
                ->whereNull('declined_by_seller_at')
                ->whereNull('cancelled_by_buyer_at')
                ->get()
                ->count();

            // get pagination properties
            $pagination = self::pagination($request->mode, $request->offset, $total);

            // get orders to confirm payment
            $orders = [];
            $result = Order::where('payment_method_id', 2)
                ->where('user_id', '>=', app()->environment('production') ? 3 : 1)
                ->whereNotNull('payment_screenshot')
                ->whereNull('payment_confirmed_at')
                ->whereNull('payment_declined_at')
                ->whereNull('declined_by_seller_at')
                ->whereNull('cancelled_by_buyer_at')
                ->skip($pagination['offset'])
                ->take(self::$limit)
                ->get();

            foreach ($result as $order) {
                array_push($orders, [
                    'id'     => $order->id,
                    'buyer'  => $order->buyer,
                    'seller' => $order->seller,
                    'status' => $order->status['payment'],
                    'total'  => $order->total,

                    // front-end status
                    'confirming' => false,
                    'declining'  => false
                ]);
            }

            return response([
                'total'   => $total,
                'offset1' => $total > 0 ? $pagination['offset'] + 1 : 0,
                'offset2' => $pagination['offset2'],
                'limit'   => self::$limit,
                'orders'  => $orders,
                'host'    => $request->getSchemeAndHttpHost()
            ]);
        }
    }


    /****************************************************************************************************
     * Confirm or Decline payment
     *
     * @param Request $request
     * @return Response
     */
    public function confirm_decline_payment(Request $request)
    {
        $request->validate([
            'order_id' => 'integer|required',
            'remarks'  => 'string|nullable',
            'confirmed'=> 'integer|required',
            'auth'     => 'string|required',
        ]);

        if(!self::valid_auth($request->auth))
            return response(['error' => 'ACCESS DENIED']);
        else {
            if($order = Order::find($request->order_id)) {
                if($request->confirmed == 1) {
                    if($order->status['payment']['status'] == Order::$PAYMENT_DECLINED) {
                        return response(['error' => 'Payment for order [ ' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . ' ] is already declined.']);
                    }
                    else if($order->status['order']['status'] == Order::$ORDER_DECLINED_BY_SELLER) {
                        return response(['error' => 'Order [ ' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . ' ] is already declined by seller.']);
                    }
                    else if($order->status['order']['status'] == Order::$ORDER_CANCELLED_BY_BUYER) {
                        return response(['error' => 'Order [ ' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . ' ] is already cancelled by buyer.']);
                    }
                    else {
                        $order->update([
                            'payment_confirmed_at'     => now(),
                            'payment_declined_at'      => null,
                            'payment_declined_remarks' => null,
                        ]);

                        // confirm payment
                        return response([
                            'done' => true
                        ]);
                    }
                }
                else {
                    if($order->status['payment']['status'] == Order::$PAYMENT_CONFIRMED) {
                        return response(['error' => 'Payment for order [ ' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . ' ] is already confirmed.']);
                    }
                    $order->update([
                        'payment_declined_at'      => now(),
                        'payment_declined_remarks' => $request->remarks,
                        'payment_confirmed_at'     => null,
                    ]);

                    // increase stock
                    $order->update_stock('increase');

                    // decline payment
                    return response([
                        'done' => true
                    ]);
                }
            }
            else
                return response(['error' => 'ORDER NOT FOUND']);
        }
    }


    /****************************************************************************************************
     * Get payments to transfer to seller
     *
     * @param Request $request
     * @return Response
     */
    public function payments_to_transfer(Request $request)
    {
        // validate request
        $request->validate([
            'mode'   => 'string|required',
            'offset' => 'integer|required',
            'auth'   => 'string|required',
        ]);

        if(!self::valid_auth($request->auth))
            return response(['error' => 'ACCESS DENIED']);
        else {
            // get total orders to transfer payment
            $total = Order::select('id')
                ->where('user_id', '>=', app()->environment('production') ? 3 : 1)
                ->where('payment_method_id', 2)
                ->whereNotNull('payment_screenshot')
                ->whereNotNull('payment_confirmed_at')
                ->whereNull('payment_declined_at')
                ->whereNull('sales_transferred_at')
                ->get()
                ->count();

            // get pagination properties
            $pagination = self::pagination($request->mode, $request->offset, $total);

            // get orders to transfer payment
            $orders = [];
            $result = Order::where('payment_method_id', 2)
                ->where('user_id', '>=', app()->environment('production') ? 3 : 1)
                ->whereNotNull('payment_screenshot')
                ->whereNotNull('payment_confirmed_at')
                ->whereNull('payment_declined_at')
                ->whereNull('sales_transferred_at')
                ->skip($pagination['offset'])
                ->take(self::$limit)
                ->orderBy('payment_confirmed_at', 'DESC')
                ->get();

            foreach ($result as $order) {
                array_push($orders, [
                    'id'     => $order->id,
                    'buyer'  => $order->buyer,
                    'seller' => $order->seller,
                    'status' => $order->status['payment'],
                    'total'  => $order->total_after_service_fee,
                    'seller_gcash' => $order->seller_gcash,

                    // front-end status
                    'transferring' => false,
                    'rollingback'  => false
                ]);
            }

            return response([
                'total'   => $total,
                'offset1' => $total > 0 ? $pagination['offset'] + 1 : 0,
                'offset2' => $pagination['offset2'],
                'limit'   => self::$limit,
                'orders'  => $orders,
                'host'    => $request->getSchemeAndHttpHost()
            ]);
        }
    }

}
