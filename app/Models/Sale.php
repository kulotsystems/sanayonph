<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'snyn_sales';

    protected $fillable = [
        'order_id',
        'product_id',

        'product_name',
        'product_description',
        'product_published_at',
        'product_image',
        'product_price',
        'product_stock',
        'product_label',
        'category_name',
        'service_fee_percentage',

        'quantity',
        'price_after_quantity',
        'shipping_fee_after_quantity',
        'price_after_shipping',
        'service_fee_after_shipping',
        'price_after_service_fee'
    ];

    protected $hidden = [
        'id',
        'order_id',
        'product_live',
        'review',
        'created_at',
        'updated_at'
    ];


    /****************************************************************************************************
     * Sale's order
     *
     * @return BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    /****************************************************************************************************
     * Order's live product
     *
     * @return BelongsTo
     */
    public function product_live()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }


    /****************************************************************************************************
     * Sale's review
     *
     * @return HasOne
     */
    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
