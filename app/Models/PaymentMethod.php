<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'snyn_payment_methods';

    protected $fillable = [
        'name'
    ];

    protected $appends = [
        'is_cod',
        'is_gcash'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    /****************************************************************************************************
     * Is Cash on Delivery?
     *
     * @return boolean
     */
    public function getIsCodAttribute()
    {
        return $this->name === 'Cash on Delivery';
    }


    /****************************************************************************************************
     * Is Gcash?
     *
     * @return boolean
     */
    public function getIsGcashAttribute()
    {
        return $this->name === 'Gcash';
    }
}
