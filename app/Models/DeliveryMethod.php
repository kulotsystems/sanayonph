<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryMethod extends Model
{
    use HasFactory;

    protected $table = 'snyn_delivery_methods';

    protected $fillable = [
        'name'
    ];

    protected $appends = [
        'is_delivery',
        'is_pickup'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    /****************************************************************************************************
     * Is Delivery?
     *
     * @return boolean
     */
    public function getIsDeliveryAttribute()
    {
        return $this->name === 'Delivery';
    }


    /****************************************************************************************************
     * Is Pickup?
     *
     * @return boolean
     */
    public function getIsPickupAttribute()
    {
        return $this->name === 'Pickup or Meetup';
    }
}
