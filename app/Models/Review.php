<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $table = 'snyn_reviews';

    protected $fillable = [
        'rating',
        'content',
    ];

    protected $hidden = [
        'sale_id',
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


    /****************************************************************************************************
     * Reviews details
     *
     * @return array
     */
    public function details()
    {
        $user = $this->sale->order->user;

        return [
            'id'   => $this->id,
            'user' => [
                'full_name' => $user->name['full_name_1'],
                'username'  => $user->username,
                'avatar'    => $user->avatar
            ],
            'rating'         => $this->rating,
            'content'       => $this->content,
            'date_time'     => Carbon::parse($this->updated_at)->toDayDateTimeString(),
            'product_label' => $this->sale->product_label
        ];
    }
}
