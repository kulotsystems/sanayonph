<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $table = 'snyn_reviews';

    protected $fillable = [
        'rating',
        'content'
    ];

    protected $hidden = [
        'sale_id',
        'user_id',
        'created_at',
        'updated_at'
    ];


    /****************************************************************************************************
     * Reviews's sale
     *
     * @return BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
