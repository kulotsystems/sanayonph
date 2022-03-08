<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Variation extends Model
{
    use HasFactory;

    protected $table = 'snyn_variations';

    protected $fillable = [
        'product_id',
        'index',
        'title'
    ];

    protected $hidden = [
        'product_id',
        'created_at',
        'updated_at'
    ];


    /****************************************************************************************************
     * Variation's items
     *
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }


    /****************************************************************************************************
     * Variation's product
     *
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
