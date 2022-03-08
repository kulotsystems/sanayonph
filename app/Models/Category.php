<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $table = 'snyn_categories';

    protected $fillable = [
        'store_id',
        'name',
        'description'
    ];

    protected $appends = [
        'unpublished_products',
        'published_products',
    ];

    protected $hidden = [
        'store_id',
        'products',
        'unpublished_products',
        'published_products',
        'created_at',
        'updated_at'
    ];


    /****************************************************************************************************
     * Category's Store
     *
     * @return BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }


    /****************************************************************************************************
     * Category's products
     *
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }


    /****************************************************************************************************
     * Category's unpublished products
     *
     * @return array
     */
    public function getUnpublishedProductsAttribute() {
        $products = [];
        foreach ($this->products->where('is_published', 0) as $product) {
            array_push($products, $product);
        }
        return $products;
    }


    /****************************************************************************************************
     * Category's published products
     *
     * @return array
     */
    public function getPublishedProductsAttribute() {
        $products = [];
        foreach ($this->products->where('is_published', 1) as $product) {
            array_push($products, $product);
        }
        return $products;
    }
}
