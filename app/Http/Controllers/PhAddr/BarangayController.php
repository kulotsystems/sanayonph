<?php

namespace App\Http\Controllers\PhAddr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhAddr\PhBarangay;

class BarangayController extends Controller
{
    /****************************************************************************************************
     * Get all barangays in a muncity
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'muncity_id' => 'required|int'
        ]);

        return response(PhBarangay::get_all($request->muncity_id));
    }
}
