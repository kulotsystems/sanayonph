<?php

namespace App\Http\Controllers\PhAddr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhAddr\PhMuncity;

class MuncityController extends Controller
{
    /****************************************************************************************************
     * Get all muncities in a province
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'province_id' => 'required|int'
        ]);

        return response(PhMuncity::get_all($request->province_id));
    }
}
