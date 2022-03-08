<?php

namespace App\Models\PhAddr;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhMuncity extends Model
{
    use HasFactory;

    protected $table = 'ph_muncities';

    // ALLOWED: 'BAAO', 'BALATAN', 'BATO', 'BUHI', 'BULA', 'IRIGA CITY', 'NABUA', ...
    public static $allowed = [ 594, 595, 596, 598, 599, 609, 616 ];

    protected $hidden = [
        'province_id'
    ];


    /****************************************************************************************************
     * Get all PhMuncity
     *
     * @param  int $province_id
     * @return PhMuncity[]|Collection
     */
    public static function get_all($province_id)
    {
        if(sizeof(PhProvince::$allowed) > 0) {
            if(!in_array($province_id, PhProvince::$allowed))
                $province_id = 0;
        }

        if(sizeof(self::$allowed) > 0)
            return PhMuncity::where('province_id', $province_id)->whereIn('id', self::$allowed)->get(['id', 'name']);
        else
            return PhMuncity::where('province_id', $province_id)->get(['id', 'name']);
    }


    /****************************************************************************************************
     * Get all PhBarangay of PhMuncity
     *
     * @return PhBarangay[]|Collection
     */
    public function barangays()
    {
        return PhBarangay::get_all($this->id);
    }


    /****************************************************************************************************
     * Get PhProvince of PhMuncity
     *
     * @return BelongsTo
     */
    public function province()
    {
        return $this->belongsTo(PhProvince::class);
    }
}
