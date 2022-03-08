<?php

namespace App\Http\Controllers\PhAddr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhAddr\PhProvince;

class ProvinceController extends Controller
{
    /****************************************************************************************************
     * Get all provinces in a region
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'region_id' => 'required|int'
        ]);

        return response(PhProvince::get_all($request->region_id));
    }
}
