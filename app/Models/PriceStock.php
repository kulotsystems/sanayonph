<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceStock extends Model
{
    use HasFactory;

    protected $table = 'snyn_prices_stocks';

    protected $fillable = [
        'product_id',
        'price_stock_mode',
        'var1_item_index',
        'var2_item_index',
        'price',
        'stock'
    ];

    protected $hidden = [
        'product_id',
        'created_at',
        'updated_at'
    ];


    /****************************************************************************************************
     * PriceStock's product
     *
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
