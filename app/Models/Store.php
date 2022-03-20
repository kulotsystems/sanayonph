<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Sanayon;


class Store extends Model
{
    use HasFactory;

    protected $table = 'snyn_stores';

    protected $fillable = [
        'user_id',
        'slug',
        'name',
        'description',
        'avatar',
        'verified_at',
        'merchant_code'
    ];

    protected $hidden = [
        'user_id',
        'merchant_code',
        'created_at',
        'updated_at'
    ];


    /****************************************************************************************************
     * Store's user
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /****************************************************************************************************
     * Store's categories
     *
     * @return HasMany
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }


    /****************************************************************************************************
     * Store's orders
     *
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    /****************************************************************************************************
     * Get stores with published products
     *
     * @return Store[]
     */
    public static function published()
    {
        return Store::whereHas('categories', function($query) {
            $query->whereHas('products', function($query) {
                $query->where('is_published', 1);
            });
        })->where('id', '>=', app()->environment('production') ? 3 : 1);
    }


    /****************************************************************************************************
     * Get product details
     *
     * @param Product $product
     * @param $action 'display' | 'buy'
     * @return array
     */
    public static function get_product_details(Product $product, $action)
    {
        $product->category;
        $product->makeVisible('category');
        $product->variations;
        $product->makeVisible('variations');
        $product->prices_stocks;
        $product->makeVisible('prices_stocks');
        $product->reviews = $product->reviews();

        $delivery_methods   = [];
        $payment_methods    = [];
        $delivery_addresses = [];
        $service_fees       = [];
        $shipping_fees      = [];
        if($action == 'buy') {
            $delivery_methods   = DeliveryMethod::all();
            $payment_methods    = PaymentMethod::all();
            $delivery_addresses = Auth::user() ? Auth::user()->plain_delivery_addresses() : [];
            $service_fees       = Sanayon::service_fees();
            $shipping_fees      = Sanayon::shipping_fees();
        }

        return [
            'product'            => $product,
            'delivery_methods'   => $delivery_methods,
            'payment_methods'    => $payment_methods,
            'delivery_addresses' => $delivery_addresses,
            'service_fees'       => $service_fees,
            'shipping_fees'      => $shipping_fees
        ];
    }


    /****************************************************************************************************
     * Compute order
     * Clone of computations inside `ProfileGuestStoreProductsDetails.vue`
     *
     * @param Request $request_param
     * @param $product_details
     * @return array
     * @throws ValidationException
     */
    public static function compute_order(Request $request_param, $product_details)
    {
        $config = [
            'product'           => $product_details['product'],
            'deliveryAddresses' => $product_details['delivery_addresses'],
            'deliveryMethods'   => $product_details['delivery_methods'],
            'paymentMethods'    => $product_details['payment_methods'],
            'serviceFees'       => $product_details['service_fees'],
            'shippingFees'      => $product_details['shipping_fees']
        ];

        $request = [
            'var1_index'       => $request_param->var1_index,
            'var2_index'       => $request_param->var2_index,
            'quantity'         => $request_param->quantity,
            'delivery_method'  => $request_param->delivery_method,
            'delivery_address' => $request_param->delivery_address,
            'payment_method'   => $request_param->payment_method,
            'final_price'      => $request_param->final_price,
        ];

        // ***************************************************************************************************
        // GET PRODUCT OVERVIEW
        $productOverview = self::productOverview($config, $request);

        // validate $productOverview
        if($productOverview['price'] == null || $productOverview['stock'] == null || $productOverview['label'] == null) {
            throw ValidationException::withMessages([
                'product' => ['The product price or stock was updated in this store.']
            ]);
        }
        else if($productOverview['stock'] <= 0) {
            throw ValidationException::withMessages([
                'stock' => ['This product is no longer in stock.']
            ]);
        }

        else if($productOverview['stock'] < $request['quantity']) {
            throw ValidationException::withMessages([
                'stock' => ['There ' . ($productOverview['stock'] > 1 ? 'are' : 'is') . ' now only ' . $productOverview['stock'] . ' available stock for the chosen variation.']
            ]);
        }

        // ***************************************************************************************************
        // GET FINAL DELIVERY METHODS
        $finalDeliveryMethods = self::finalDeliveryMethods($config, $request);

        // validate chosen delivery method
        $validDeliveryMethod = false;
        foreach ($finalDeliveryMethods as $deliveryMethod) {
            if($deliveryMethod['id'] == $request['delivery_method']) {
                $validDeliveryMethod = true;
                break;
            }
        }
        if(!$validDeliveryMethod) {
            throw ValidationException::withMessages([
                'delivery_method' => ['Selected Delivery Method is no longer available.']
            ]);
        }
        else if(self::deliveryMethodIsNotAvailable(self::deliveryMethod($config, $request))) {
            throw ValidationException::withMessages([
                'delivery_method' => ['Selected Delivery Method is not yet available.']
            ]);
        }

        // ***************************************************************************************************
        // GET FINAL DELIVERY ADDRESS
        $finalDeliveryAddressID = null;
        $finalDeliveryAddress   = '';
        if($request['delivery_method'] == '1') {
            foreach ($config['deliveryAddresses'] as $deliveryAddress) {
                if ($deliveryAddress['id'] == $request['delivery_address']) {
                    $finalDeliveryAddressID = $deliveryAddress['id'];
                    $finalDeliveryAddress   = $deliveryAddress['address'];
                    break;
                }
            }
        }

        // validate final delivery address
        if($request['delivery_method'] == '1' && $finalDeliveryAddress == '') {
            throw ValidationException::withMessages([
                'delivery_address' => ['The chosen Delivery Address is no longer available.']
            ]);
        }

        // ***************************************************************************************************
        // VALIDATE PAYMENT METHOD
        $valid_payment_method = false;
        foreach ($config['paymentMethods'] as $paymentMethod) {
            if(intval($request['payment_method']) == $paymentMethod['id']) {
                $valid_payment_method = true;
                break;
            }
        }

        if(!$valid_payment_method) {
            throw ValidationException::withMessages([
                'payment_method' => ['The chosen Payment Method is no longer available.']
            ]);
        }


        // ***************************************************************************************************
        // GET PRICE
        $priceAfterQuantity       = self::priceAfterQuantity($productOverview, $request);
        $shippingFeeAfterQuantity = self::shippingFeeAfterQuantity($config, $request);
        $priceAfterShipping       = self::priceAfterShipping($priceAfterQuantity, $shippingFeeAfterQuantity, $config, $request);
        $serviceFeeAfterShipping  = self::serviceFeeAfterShipping($priceAfterShipping, $config);
        $priceAfterServiceFee     = self::priceAfterServiceFee($priceAfterShipping, $serviceFeeAfterShipping);

        // validate final price
        if($priceAfterServiceFee != $request['final_price']) {
            throw ValidationException::withMessages([
                'product' => ['The product pricing was updated in this store. Computed: ' . $priceAfterServiceFee . '.']
            ]);
        }

        // ***************************************************************************************************
        return [
            'config'   => $config,
            'request'  => $request,
            'computed' => [
                'productOverview'          => $productOverview,
                'priceAfterQuantity'       => $priceAfterQuantity,
                'shippingFeeAfterQuantity' => $shippingFeeAfterQuantity,
                'priceAfterShipping'       => $priceAfterShipping,
                'serviceFeeAfterShipping'  => $serviceFeeAfterShipping,
                'priceAfterServiceFee'     => $priceAfterServiceFee,
                'finalDeliveryAddressID'   => $finalDeliveryAddressID,
                'finalDeliveryAddress'     => $finalDeliveryAddress
            ]
        ];
    }


    /****************************************************************************************************
     * computed :: productOverview()
     *
     * @param $config
     * @param $request
     * @return array
     */
    private static function productOverview(&$config, &$request)
    {
        $overview = [
            'image'=> null,
            'price'=> null,
            'stock'=> null,
            'label'=> null,
        ];

        if($config['product']['price_stock_mode'] === 'var1_only') {
            if($request['var1_index'] >= 0) {
                $overview['image'] = $config['product']['variations'][0]['items'][$request['var1_index']]['image'];
                $overview['label'] = $config['product']['variations'][0]['items'][$request['var1_index']]['label'];
                for($i=0; $i<sizeof($config['product']['prices_stocks']); $i++) {
                    $priceStock = $config['product']['prices_stocks'][$i];
                    if($priceStock['var1_item_index'] === $request['var1_index']) {
                        $overview['price'] = $priceStock['price'];
                        $overview['stock'] = $priceStock['stock'];
                        break;
                    }
                }
            }
        }
        else if($config['product']['price_stock_mode'] === 'var2_only') {
            if($request['var2_index'] >= 0) {
                $overview['image'] = $config['product']['variations'][1]['items'][$request['var2_index']]['image'];
                $overview['label'] = $config['product']['variations'][1]['items'][$request['var2_index']]['label'];
                for($i=0; $i<sizeof($config['product']['prices_stocks']); $i++) {
                    $priceStock = $config['product']['prices_stocks'][$i];
                    if($priceStock['var2_item_index'] === $request['var2_index']) {
                        $overview['price'] = $priceStock['price'];
                        $overview['stock'] = $priceStock['stock'];
                        break;
                    }
                }
            }
        }
        else if($config['product']['price_stock_mode'] === 'both_vars') {
            if($request['var1_index'] >=0 && $request['var2_index'] >= 0) {
                $overview['image'] = $config['product']['variations'][0]['items'][$request['var1_index']]['image'];
                if($overview['image'] == null) $overview['image'] = $config['product']['variations'][1]['items'][$request['var2_index']]['image'];
                $overview['label'] = $config['product']['variations'][0]['items'][$request['var1_index']]['label'] . ', ' . $config['product']['variations'][1]['items'][$request['var2_index']]['label'];
                for($i=0; $i<sizeof($config['product']['prices_stocks']); $i++) {
                    $priceStock = $config['product']['prices_stocks'][$i];
                    if($priceStock['var1_item_index'] === $request['var1_index'] && $priceStock['var2_item_index'] === $request['var2_index']) {
                        $overview['price'] = $priceStock['price'];
                        $overview['stock'] = $priceStock['stock'];
                        break;
                    }
                }
            }
        }

        // verify image overview
        if($overview['image'] == null) {
            if(sizeof($config['product']['gen_images']) > 0)
                $overview['image'] = $config['product']['gen_images'][0];
        }

        return $overview;
    }


    /****************************************************************************************************
     * computed :: finalDeliveryMethods()
     *
     * @param $config
     * @param $request
     * @return array
     */
    private static function finalDeliveryMethods(&$config, &$request)
    {
        $deliveryMethods = [];

        for($i=0; $i<sizeof($config['deliveryMethods']); $i++) {
            $deliveryMethod = $config['deliveryMethods'][$i];
            if($deliveryMethod->is_pickup) {
                // confirm pickup
                if(!$config['product']['allows_pickup'])
                    continue;
            }
            array_push($deliveryMethods, $deliveryMethod);
        }

        return $deliveryMethods;
    }


    /****************************************************************************************************
     * computed :: priceAfterQuantity()
     *             price after multiplying with quantity
     *
     * @param $productOverview
     * @param $request
     * @return int
     */
    private static function priceAfterQuantity(&$productOverview, &$request)
    {
        $price = 0;
        if($productOverview['price'] != null)
            $price = $productOverview['price'] * $request['quantity'];
        return $price;
    }


    /****************************************************************************************************
     * computed :: shippingFeeAfterQuantity()
     *             shipping fee after applying quantity
     *
     * @param $config
     * @param $request
     * @return array
     */
    private static function shippingFeeAfterQuantity(&$config, &$request)
    {
        $fee = [
            'max_quantity' => 0,
            'operation'    => '',
            'amount'       => 0
        ];
        if(self::deliveryMethod($config, $request)->is_delivery) {
            for($i=0; $i<sizeof($config['shippingFees']); $i++) {
                $shipping_fee = $config['shippingFees'][$i];
                if($request['quantity'] <= $shipping_fee['max_quantity']) {
                    $fee['max_quantity'] = $shipping_fee['max_quantity'];
                    $fee['operation']    = $shipping_fee['operation'];
                    if($shipping_fee['operation'] == '=')
                        $fee['amount'] = $shipping_fee['amount'];
                    else if($shipping_fee['operation'] == '*')
                        $fee['amount'] = $shipping_fee['amount'] * $request['quantity'];
                    break;
                }
            }
        }

        return $fee;
    }


    /****************************************************************************************************
     * computed :: priceAfterShipping()
     *             price after applying shipping fee
     *
     * @param $priceAfterQuantity
     * @param $shippingFeeAfterQuantity
     * @param $config
     * @param $request
     * @return int
     */
    private static function priceAfterShipping(&$priceAfterQuantity, $shippingFeeAfterQuantity, &$config, &$request)
    {
        return $priceAfterQuantity +  $shippingFeeAfterQuantity['amount'];
    }


    /****************************************************************************************************
     * computed :: serviceFeeAfterShipping()
     *             service fee after applying shipping fee
     *
     * @param $priceAfterShipping
     * @param $config
     * @return array
     */
    private static function serviceFeeAfterShipping(&$priceAfterShipping, &$config)
    {
        $fee = [
            'percent' => 0,
            'amount'  => 0
        ];
        if(isset($config['serviceFees'])) {
            for($i=0; $i<sizeof($config['serviceFees']); $i++) {
                $service_fee = $config['serviceFees'][$i];
                if($priceAfterShipping <= $service_fee['max_order']) {
                    $fee['percent'] = $service_fee['percent'];
                    $fee['amount']  = round($priceAfterShipping * ($service_fee['percent'] / 100), 0);
                    break;
                }
            }
        }
        return $fee;
    }


    /****************************************************************************************************
     * computed :: priceAfterServiceFee()
     *             price after applying service fee
     *
     * @param $priceAfterShipping
     * @param $serviceFeeAfterShipping
     * @return int
     */
    private static function priceAfterServiceFee(&$priceAfterShipping, &$serviceFeeAfterShipping)
    {
        return $priceAfterShipping - $serviceFeeAfterShipping['amount'];
    }


    /****************************************************************************************************
     * computed :: deliveryMethod()
     *
     * @param $config
     * @param $request
     * @return DeliveryMethod
     */
    public static function deliveryMethod(&$config, &$request)
    {
        $delivery_method = null;
        for($i=0; $i<sizeof($config['deliveryMethods']); $i++) {
            if($config['deliveryMethods'][$i]->id == $request['delivery_method']) {
                $delivery_method = $config['deliveryMethods'][$i];
                break;
            }
        }
        return $delivery_method;
    }


    /****************************************************************************************************
     * computed :: paymentMethod()
     *
     * @param $config
     * @param $request
     * @return PaymentMethod
     */
    public static function paymentMethod(&$config, &$request)
    {
        $payment_method = null;
        for($i=0; $i<sizeof($config['paymentMethods']); $i++) {
            if($config['paymentMethods'][$i]->id == $request['payment_method']) {
                $payment_method = $config['paymentMethods'][$i];
                break;
            }
        }
        return $payment_method;
    }


    /****************************************************************************************************
     * computed :: deliveryMethodIsNotAvailable()
     *
     * @param $deliveryMethod
     * @return bool
     */
    public static function deliveryMethodIsNotAvailable($deliveryMethod)
    {
        $bool = false;
        if($deliveryMethod != null) {
            if($deliveryMethod->is_delivery)
                $bool = true;
        }
        return $bool;
    }

}
