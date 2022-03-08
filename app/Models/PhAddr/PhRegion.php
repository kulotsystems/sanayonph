<?php

namespace App\Models\PhAddr;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhRegion extends Model
{
    use HasFactory;

    protected $table = 'ph_regions';

    // ALLOWED: 'REGION V', ...
    public static $allowed = [ 6 ];


    /****************************************************************************************************
     * Get all PhRegion
     *
     * @return PhRegion[]|Collection
     */
    public static function get_all()
    {
        if(sizeof(self::$allowed) > 0)
            return PhRegion::whereIn('id', self::$allowed)->get(['id', 'name']);
        else
            return PhRegion::all(['id', 'name']);
    }
}
