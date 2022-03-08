<?php

namespace App\Http\Controllers\PhAddr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhAddr\PhRegion;

class RegionController extends Controller
{
    /****************************************************************************************************
     * Get all regions
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response(PhRegion::get_all());
    }
}
