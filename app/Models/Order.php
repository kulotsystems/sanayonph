<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Order extends Model
{
    use HasFactory;

    protected $table = 'snyn_orders';

    public static $PAYMENT_NOT_AVAILABLE     = 'Not yet available';
    public static $PAYMENT_FOR_SCREENSHOT    = 'For screenshot';
    public static $PAYMENT_FOR_CONFIRMATION  = 'For confirmation';
    public static $PAYMENT_CONFIRMED         = 'Confirmed';
    public static $PAYMENT_DECLINED          = 'Declined';
    public static $DELIVERY_NOT_AVAILABLE    = 'Not yet available';
    public static $DELIVERY_SHIPPED          = 'Shipped';
    public static $DELIVERY_FOR_PICKUP       = 'For pickup / meetup';
    public static $DELIVERY_RECEIVED         = 'Received';
    public static $ORDER_CANCELLED_BY_BUYER  = 'Cancelled by buyer';
    public static $ORDER_DECLINED_BY_SELLER  = 'Declined by seller';
    public static $ORDER_FOR_PAYMENT         = 'For payment';
    public static $ORDER_PAYMENT_DECLINED    = 'Payment Declined';
    public static $ORDER_FOR_CONFIRMATION    = 'For confirmation';
    public static $ORDER_FOR_DELIVERY        = 'For delivery';
    public static $ORDER_FOR_PICKUP          = 'For pickup / meetup';
    public static $ORDER_COMPLETED           = 'Completed';

    protected $fillable = [
        'user_id',
        'store_id',
        'delivery_method_id',
        'payment_method_id',

        'delivery_address_id',
        'delivery_address',
        'delivery_fullname',
        'delivery_mobile',
        'delivery_email',
        'ordered_at',
        'confirmed_by_seller_at',
        'cancelled_by_buyer_at',
        'reason_by_buyer',
        'declined_by_seller_at',
        'reason_by_seller',

        'payment_screenshot',
        'payment_screenshot_at',
        'payment_confirmed_at',
        'payment_declined_at',
        'payment_declined_remarks',

        'shipped_at',
        'courier',
        'tracking_code',
        'pickup_at',
        'pickup_location',
        'received_at',
        'sales_transferred_at'
    ];

    protected $appends = [
        'ordered_at_str',
        'seller',
        'seller_gcash',
        'buyer',
        'total',
        'total_after_service_fee',
        'status'
    ];

    protected $hidden = [
        'user_id',
        'delivery_method_id',
        'payment_method_id',
        'ordered_at',
        'payment_screenshot',
        'payment_screenshot_at',
        'payment_confirmed_at',
        'payment_declined_at',
        'payment_declined_remarks',
        'confirmed_by_seller_at',
        'cancelled_by_buyer_at',
        'reason_by_buyer',
        'cancelled_by_seller_at',
        'reason_by_seller',
        'delivery_address_id',
        'delivery_address',
        'delivery_fullname',
        'delivery_mobile',
        'delivery_email',
        'shipped_at',
        'courier',
        'tracking_code',
        'pickup_at',
        'pickup_location',
        'received_at',
        'sales_transferred_at',
        'sales',
        'user',
        'seller',
        'buyer',
        'total',
        'total_after_service_fee',
        'created_at',
        'updated_at'
    ];


    /****************************************************************************************************
     * Order's user
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /****************************************************************************************************
     * Order's delivery method
     *
     * @return BelongsTo
     */
    public function delivery_method()
    {
        return $this->belongsTo(DeliveryMethod::class);
    }


    /****************************************************************************************************
     * Order's payment method
     *
     * @return BelongsTo
     */
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }


    /****************************************************************************************************
     * Order's sales
     *
     * @return HasMany
     */
    public function sales()
    {
        return $this->HasMany(Sale::class);
    }


    /****************************************************************************************************
     * Order's ordered_at string
     *
     * @return string
     */
    public function getOrderedAtStrAttribute()
    {
        return Carbon::parse($this->ordered_at)->toDayDateTimeString();
    }


    /****************************************************************************************************
     * Order's seller
     *
     * @return array
     */
    public function getSellerAttribute()
    {
        $seller = null;
        foreach ($this->sales as $sale) {
            $seller = $sale->product_live->category->store->user->seller_info();
            break;
        }
        return $seller;
    }


    /****************************************************************************************************
     * Order's seller gcash
     *
     * @return array
     */
    public function getSellerGcashAttribute()
    {
        $gcash = null;
        foreach ($this->sales as $sale) {
            $gcash = $sale->product_live->category->store->user->gcash_info();
            break;
        }
        return $gcash;
    }


    /****************************************************************************************************
     * Order's buyer
     *
     * @return array
     */
    public function getBuyerAttribute()
    {
        return $this->user->seller_info();
    }


    /****************************************************************************************************
     * Order's total
     *
     * @return int
     */
    public function getTotalAttribute()
    {
        $total = 0;
        foreach ($this->sales as $sale) {
            $total += $sale->price_after_shipping;
        }
        return $total;
    }


    /****************************************************************************************************
     * Order's total after service fee
     *
     * @return int
     */
    public function getTotalAfterServiceFeeAttribute()
    {
        $total = 0;
        foreach ($this->sales as $sale) {
            $total += $sale->price_after_shipping - $sale->service_fee_after_shipping;
        }
        return $total;
    }


    /****************************************************************************************************
     * Order's status
     *
     * @return array
     */
    public function getStatusAttribute()
    {
        $status = [
            'order' => [
                'status'       => null,
                'date_time'    => null,
                'remarks'      => null,
            ],
            'payment' => [
                'status'       => null,
                'date_time'    => null,
                'screenshot'   => null,
                'remarks'      => null,
            ],
            'delivery' => [
                'status'        => null,
                'date_time'     => null,
                'location'      => null,
                'full_name'     => null,
                'mobile'        => null,
                'email'         => null,
                'courier'       => null,
                'tracking_code' => null,
                'remarks'       => null,
            ],
        ];


        // GET PAYMENT STATUS
        if($this->payment_method->is_cod) {
            $status['payment']['status'] = self::$PAYMENT_NOT_AVAILABLE;
        }
        else if($this->payment_method->is_gcash) {
            if(!$this->payment_screenshot)
                $status['payment']['status'] = self::$PAYMENT_FOR_SCREENSHOT;
            else if(!$this->payment_confirmed_at && !$this->payment_declined_at) {
                $status['payment']['status']    = self::$PAYMENT_FOR_CONFIRMATION;
                $status['payment']['date_time'] = Carbon::parse($this->payment_screenshot_at)->toDayDateTimeString();
            }
            else if($this->payment_confirmed_at) {
                $status['payment']['status']    = self::$PAYMENT_CONFIRMED;
                $status['payment']['date_time'] = Carbon::parse($this->payment_confirmed_at)->toDayDateTimeString();
            }
            else if($this->payment_declined_at) {
                $status['payment']['status']    = self::$PAYMENT_DECLINED;
                $status['payment']['date_time'] = Carbon::parse($this->payment_declined_at)->toDayDateTimeString();
                $status['payment']['remarks']   = $this->payment_declined_remarks;
            }
        }
        $status['payment']['screenshot'] = $this->payment_screenshot;


        // GET DELIVERY STATUS
        $status['delivery']['status'] = self::$DELIVERY_NOT_AVAILABLE;
        if($this->received_at) {
            $status['delivery']['status']    = self::$DELIVERY_RECEIVED;
            $status['delivery']['date_time'] = Carbon::parse($this->received_at)->toDayDateTimeString();
            $status['delivery']['location']  = $this->delivery_method->is_pickup ? $this->pickup_location : $this->delivery_address;
        }
        else if($this->pickup_at) {
            $status['delivery']['status']    = self::$DELIVERY_FOR_PICKUP;
            $status['delivery']['date_time'] = Carbon::parse($this->pickup_at)->toDayDateTimeString();
            $status['delivery']['location']  = $this->pickup_location;
        }
        else if($this->shipped_at) {
            $status['delivery']['status']    = self::$DELIVERY_SHIPPED;
            $status['delivery']['date_time'] = Carbon::parse($this->shipped_at)->toDayDateTimeString();
            $status['delivery']['location']  = $this->delivery_address;
        }
        $status['delivery']['full_name'] = $this->delivery_fullname;
        $status['delivery']['mobile']    = $this->delivery_mobile;
        $status['delivery']['email']     = $this->delivery_email;


        // GET ORDER STATUS
        if($status['delivery']['status'] == self::$DELIVERY_RECEIVED) {
            $status['order']['status'] = self::$ORDER_COMPLETED;
        }
        else if($status['delivery']['status'] == self::$DELIVERY_SHIPPED) {
            $status['order']['status'] = self::$ORDER_FOR_DELIVERY;
        }
        else if($status['delivery']['status'] == self::$DELIVERY_FOR_PICKUP) {
            $status['order']['status'] = self::$ORDER_FOR_PICKUP;
        }
        else if($this->cancelled_by_buyer_at) {
            $status['order']['status']    = self::$ORDER_CANCELLED_BY_BUYER;
            $status['order']['date_time'] = Carbon::parse($this->cancelled_by_buyer_at)->toDayDateTimeString();
            $status['order']['remarks']   = $this->reason_by_buyer;
        }
        else if($this->declined_by_seller_at) {
            $status['order']['status']    = self::$ORDER_DECLINED_BY_SELLER;
            $status['order']['date_time'] = Carbon::parse($this->cancelled_by_seller_at)->toDayDateTimeString();
            $status['order']['remarks']   = $this->reason_by_seller;
        }
        else if($status['payment']['status'] && $status['payment']['status'] == self::$PAYMENT_CONFIRMED) {
            $status['order']['status'] = self::$ORDER_FOR_CONFIRMATION;
        }
        else if($status['payment']['status'] && $status['payment']['status'] == self::$PAYMENT_DECLINED) {
            $status['order']['status']    = self::$ORDER_PAYMENT_DECLINED;
            $status['order']['date_time'] = Carbon::parse($this->payment_declined_at)->toDayDateTimeString();
            $status['order']['remarks']   = $this->payment_declined_remarks;
        }
        else if($status['payment']['status'] && $status['payment']['status'] != self::$PAYMENT_NOT_AVAILABLE) {
            $status['order']['status'] = self::$ORDER_FOR_PAYMENT;
        }

        return $status;
    }


    /****************************************************************************************************
     * Order details for buyer
     *
     * @param $person
     * @return Order
     */
    public function details_for($person)
    {
        $order = $this;
        $order->delivery_method;
        $order->payment_method;
        $order->sales;
        $order->makeVisible('sales');

        if($person == 'buyer') {
            $order->seller;
            $order->makeVisible('seller');
            $order->total;
            $order->makeVisible('total');
        }
        else if($person == 'seller') {
            $order->buyer;
            $order->makeVisible('buyer');
            $order->total_after_service_fee;
            $order->makeVisible('total_after_service_fee');
        }
        return $order;
    }


    /****************************************************************************************************
     * Decrease or increase stock of ordered products
     *
     * @param string $mode - 'decrease' or 'increase'
     * @return void
     * @throws ValidationException
     */
    public function update_stock($mode)
    {
        $order = $this;

        // loop through sales
        foreach ($order->sales as $sale) {
            if($product = Product::find($sale->product_id)) {
                // get variations
                $variation_1 = $product->variations->where('index', 0)->first();
                $variation_2 = $product->variations->where('index', 1)->first();
                $arr_product_label = explode(', ', $sale->product_label);

                // search for $price_stock
                $price_stock = null;
                if(sizeof($arr_product_label) == 1) {
                    if($product->price_stock_mode == 'var1_only') {
                        $var1_item_label = $arr_product_label[0];
                        if($item = $variation_1->items->where('label', $var1_item_label)->first())
                            $price_stock = $product->prices_stocks->where('var1_item_index', $item->index)->first();
                    }
                    else if($product->price_stock_mode == 'var2_only') {
                        $var2_item_label = $arr_product_label[0];
                        if($item = $variation_2->items->where('label', $var2_item_label)->first())
                            $price_stock = $product->prices_stocks->where('var2_item_index', $item->index)->first();
                    }
                }
                else {
                    $var1_item_label = $arr_product_label[0];
                    $var2_item_label = $arr_product_label[1];
                    $item1 = $variation_1->items->where('label', $var1_item_label)->first();
                    $item2 = $variation_2->items->where('label', $var2_item_label)->first();
                    if($item1 && $item2)
                        $price_stock = $product->prices_stocks->where('var1_item_index', $item1->index)->where('var2_item_index', $item2->index)->first();
                }

                // modify $price_stock
                if($price_stock != null) {
                    if($mode == 'decrease') {
                        $remaining_stock = $price_stock->stock - $sale->quantity;
                        if($remaining_stock < 0) {
                            throw ValidationException::withMessages([
                                'stock' => ['Ordered quantity (' . $sale->quantity . ') is greater than the remaining stock (' . $price_stock->stock . ') for "' . $sale->product_label . '" variation.']
                            ]);
                        }
                        else {
                            $price_stock->update([
                                'stock' => $remaining_stock
                            ]);
                        }
                    }
                    else if($mode == 'increase') {
                        $remaining_stock = $price_stock->stock + $sale->quantity;
                        $price_stock->update([
                            'stock' => $remaining_stock
                        ]);
                    }
                }
            }
        }
    }
}
