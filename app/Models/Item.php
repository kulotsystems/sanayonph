<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory;

    protected $table = 'snyn_items';

    protected $fillable = [
        'variation_id',
        'index',
        'image',
        'label'
    ];

    protected $hidden = [
        'variation_id',
        'created_at',
        'updated_at'
    ];


    /****************************************************************************************************
     * Item's variation
     *
     * @return BelongsTo
     */
    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }
}
