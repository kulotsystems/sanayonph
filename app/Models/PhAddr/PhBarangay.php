<?php

namespace App\Models\PhAddr;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhBarangay extends Model
{
    use HasFactory;

    protected $table = 'ph_barangays';

    protected $hidden = [
        'muncity_id'
    ];

    public static $allowed = [];


    /****************************************************************************************************
     * Get all PhBarangay
     *
     * @param  int $muncity_id
     * @return PhBarangay[]|Collection
     */
    public static function get_all($muncity_id)
    {
        if(sizeof(PhMuncity::$allowed) > 0) {
            if(!in_array($muncity_id, PhMuncity::$allowed))
                $muncity_id = 0;
        }

        if(sizeof(self::$allowed) > 0)
            return PhBarangay::where('muncity_id', $muncity_id)->whereIn('id', self::$allowed)->get(['id', 'name']);
        else
            return PhBarangay::where('muncity_id', $muncity_id)->get(['id', 'name']);
    }


    /****************************************************************************************************
     * Get PhMuncity of PhBarangay
     *
     * @return BelongsTo
     */
    public function muncity()
    {
        return $this->belongsTo(PhMuncity::class);
    }
}
