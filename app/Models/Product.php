<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use function Symfony\Component\Finder\size;

class Product extends Model
{
    use HasFactory;

    protected $table = 'snyn_products';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price_stock_mode',
        'is_published',
        'published_at',
        'delivery_origin',
        'allows_pickup',
        'shipping_fee'
    ];

    protected $casts = [
        'is_published'  => 'boolean',
        'allows_pickup' => 'boolean'
    ];

    protected $appends = [
        'gen_images',
        'var_images',
        'pricing',
        'address'
    ];

    protected $hidden = [
        'category_id',
        'created_at',
        'updated_at',
        'category',
        'delivery_origin_address',
        'address',
        'general_images',
        'variations',
        'prices_stocks',
        'sales',
        'sales_sold',
        'sales_reviewed'
    ];

    public static $PRICE_STOCK_MODES = ['var1_only', 'var2_only', 'both_vars'];


    /****************************************************************************************************
     * Products's category
     *
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    /****************************************************************************************************
     * Products's delivery origin address
     *
     * @return BelongsTo
     */
    public function delivery_origin_address()
    {
        return $this->belongsTo(DeliveryAddress::class, 'delivery_origin');
    }


    /****************************************************************************************************
     * Product's general images
     *
     * @return HasMany
     */
    public function general_images()
    {
        return $this->hasMany(GeneralImage::class);
    }


    /****************************************************************************************************
     * Product's variations
     *
     * @return HasMany
     */
    public function variations()
    {
        return $this->hasMany(Variation::class);
    }


    /****************************************************************************************************
     * Product's prices_stocks
     *
     * @return HasMany
     */
    public function prices_stocks()
    {
        return $this->hasMany(PriceStock::class)->where('price_stock_mode', $this->price_stock_mode);
    }


    /****************************************************************************************************
     * Product's sales
     *
     * @return HasMany
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }


    /****************************************************************************************************
     * Product's sales sold
     *
     */
    public function sales_sold()
    {
        return $this->whereHas('sales', function($query) {
            $query->whereHas('order', function($query) {
                $query
                ->whereNull('cancelled_by_buyer_at')
                ->whereNull('declined_by_seller_at')
                ->whereNull('payment_declined_at');
            });
        });
    }


    /****************************************************************************************************
     * Product's sales reviewed
     *
     */
    public function sales_reviewed()
    {
        return $this->sales()->where(function($query) {
            $query->has('review');
        });
    }


    /****************************************************************************************************
     * Product's reviews
     *
     */
    public function reviews()
    {
        $reviews = [];
        foreach ($this->sales_reviewed as $sales_reviewed) {
            array_push($reviews, $sales_reviewed->review->details());
        }
        return $reviews;
    }


    /****************************************************************************************************
     * elaborate data
     *
     */
    public function elaborate()
    {
        $this->category;
        $this->delivery_origin_address;

        foreach ($this->variations as $variation) {
            $variation = $variation->items;
        }

        foreach ($this->prices_stocks as $prices_stocks) {
            $prices_stocks->price_stock_mode;
        }
    }

    /****************************************************************************************************
     * Get general images src array
     *
     * @return array
     */
    public function getGenImagesAttribute()
    {
        $images = [];
        foreach ($this->general_images as $image) {
            if($image->image != null)
                array_push($images, $image->image);
        }
        return $images;
    }


    /****************************************************************************************************
     * Get images
     *
     * @return array
     */
    public function getVarImagesAttribute()
    {
        $images = [];
        foreach ($this->variations as $variation) {
            foreach ($variation->items as $item) {
                if($item->image != '')
                    array_push($images, $item->image);
            }
        }

        return $images;
    }


    /****************************************************************************************************
     * Get pricing overview
     *
     * @return array
     */
    public function getPricingAttribute()
    {
        $prices = [];
        $stocks = [];
        $stocks_total = 0;

        foreach ($this->prices_stocks as $price_stock) {
            array_push($prices, $price_stock->price);
            array_push($stocks, $price_stock->stock);
            $stocks_total += $price_stock->stock;
        }

        return [
            'price' => [
                'min'   => sizeof($prices) > 0 ? min($prices) : 0,
                'max'   => sizeof($prices) > 0 ? max($prices) : 0
            ],
            'stock' => [
                'min'   => sizeof($stocks) > 0 ? min($stocks) : 0,
                'max'   => sizeof($stocks) > 0 ? max($stocks) : 0,
                'total' => $stocks_total
            ]
        ];
    }


    /****************************************************************************************************
     * Get address
     *
     * @return string
     */
    public function getAddressAttribute()
    {
        return $this->delivery_origin_address->address;
    }
}
