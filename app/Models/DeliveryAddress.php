<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PhAddr\PhBarangay;
use App\Models\PhAddr\PhMuncity;
use App\Models\PhAddr\PhProvince;
use App\Models\PhAddr\PhRegion;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAddress extends Model
{
    use HasFactory;

    protected $table = 'snyn_delivery_addresses';

    protected $fillable = [
        'barangay_id',
        'user_id',
        'street',
    ];

    protected $appends = [
        'address'
    ];

    protected $hidden = [
        'barangay_id',
        'user_id',
        'created_at',
        'updated_at'
    ];


    /****************************************************************************************************
     * Barangay address
     *
     * @return BelongsTo
     */
    public function barangay()
    {
        return $this->belongsTo(PhAddr\PhBarangay::class);
    }


    /****************************************************************************************************
     * Complete origin address
     *
     * @return array
     */
    public function origin()
    {
        $this->barangay->muncity->province->region;

        $barangay_id = $this->barangay->id;
        $muncity_id  = $this->barangay->muncity->id;
        $province_id = $this->barangay->muncity->province->id;
        $region_id   = $this->barangay->muncity->province->region->id;

        return [
            'regions'   => PhRegion::get_all(),
            'provinces' => PhProvince::get_all($region_id),
            'muncities' => PhMuncity::get_all($province_id),
            'barangays' => PhBarangay::get_all($muncity_id),
            'barangay_id' => $barangay_id,
            'muncity_id'  => $muncity_id,
            'province_id' => $province_id,
            'region_id'   => $region_id,
            'street'      => $this->street
        ];
    }


    /****************************************************************************************************
     * Detailed cascading address
     *
     * @return $this
     */
    public function cascade()
    {
        $this->barangay->muncity->province->region;
        return $this;
    }


    /****************************************************************************************************
     * Generate address attribute
     *
     * @return string
     */
    public function getAddressAttribute()
    {
        return $this->street . ', ' .
               $this->barangay->name . ', ' .
               $this->barangay->muncity->name . ', ' .
               $this->barangay->muncity->province->name . ' ' .
               '(' . $this->barangay->muncity->province->region->name . ')';
    }


    /****************************************************************************************************
     * Muncity Address
     *
     * @return string
     */
    public function muncity_address()
    {
        return $this->barangay->muncity->name . ', ' . $this->barangay->muncity->province->name;
    }
}
