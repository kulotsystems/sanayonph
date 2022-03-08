<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralImage extends Model
{
    use HasFactory;

    protected $table = 'snyn_general_images';

    protected $fillable = [
        'product_id',
        'image'
    ];

    protected $hidden = [
        'product_id',
        'created_at',
        'updated_at'
    ];


    /****************************************************************************************************
     * GeneralImage's product
     *
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
