<?php

namespace App\Models\PhAddr;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhProvince extends Model
{
    use HasFactory;

    protected $table = 'ph_provinces';

    // ALLOWED: 'ALBAY', 'CAMARINES SUR', ...
    public static $allowed = [ 27, 29 ];

    protected $hidden = [
        'region_id'
    ];


    /****************************************************************************************************
     * Get all PhProvince
     *
     * @param  int $region_id
     * @return PhProvince[]|Collection
     */
    public static function get_all($region_id)
    {
        if(sizeof(PhRegion::$allowed) > 0) {
            if(!in_array($region_id, PhRegion::$allowed))
                $region_id = 0;
        }

        if(sizeof(self::$allowed) > 0)
            return PhProvince::where('region_id', $region_id)->whereIn('id', self::$allowed)->get(['id', 'name']);
        else
            return PhProvince::where('region_id', $region_id)->get(['id', 'name']);
    }


    /****************************************************************************************************
     * Get all PhMuncity of PhProvince
     *
     * @return PhMuncity[]|Collection
     */
    public function muncities()
    {
        return PhMuncity::get_all($this->id);
    }


    /****************************************************************************************************
     * Get PhRegion of PhProvince
     *
     * @return BelongsTo
     */
    public function region()
    {
        return $this->belongsTo(PhRegion::class);
    }
}
