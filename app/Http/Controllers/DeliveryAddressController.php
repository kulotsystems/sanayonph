<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryAddress;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DeliveryAddressController extends Controller
{

    /****************************************************************************************************
     * Display a listing of user's delivery addresses.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $addresses = [];
        $user = $request->mddlwr_user;
        foreach ($user->delivery_addresses as $address) {
            array_push($addresses, $address->cascade());
        }

        return response([
            'addresses' => $addresses
        ]);
    }


    /****************************************************************************************************
     *  Store a newly created delivery address.
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'barangay' => 'int|required',
            'street'   => 'string|required'
        ]);

        $user   = $request->mddlwr_user;
        $street = Str::upper($request->street);
        if($user->delivery_addresses->where('barangay_id', $request->barangay)->where('street', $street)->first() === null) {
            $delivery_address = new DeliveryAddress();
            $delivery_address->barangay_id = $request->barangay;
            $delivery_address->user_id = Auth::user()->id;
            $delivery_address->street  = $street;
            $delivery_address->save();

            return response([
                'stored' => true
            ]);
        }
        else {
            throw ValidationException::withMessages([
                'street' => ['New address already exists.']
            ]);
        }
    }


    /****************************************************************************************************
     * Fetch data for editing a delivery address.
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $delivery_address = $request->mddlwr_delivery_address;
        return response([
            'origin'  => $delivery_address->origin(),
            'address' => $delivery_address->address
        ]);
    }


    /****************************************************************************************************
     * Update the specified delivery address.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'barangay' => 'int|required',
            'street'   => 'string|required'
        ]);

        $delivery_address = $request->mddlwr_delivery_address;
        $street = Str::upper($request->street);

        if($request->mddlwr_user->delivery_addresses->where('id', '!=', $id)->where('barangay_id', $request->barangay)->where('street', $street)->first() === null) {
            $delivery_address->barangay_id = $request->barangay;
            $delivery_address->street = $street;
            $delivery_address->update();

            return response([
                'updated' => true
            ]);
        }
        else {
            throw ValidationException::withMessages([
                'street' => ['Updated address already exists.']
            ]);
        }
    }


    /****************************************************************************************************
     * Remove the specified delivery address.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->mddlwr_user;
        if($delivery_address = $user->delivery_addresses->find($id)) {
            $delivery_address->delete();

            return response([
                'deleted' => true
            ]);
        }
        else {
            throw ValidationException::withMessages([
                'delivery_address' => ['Delivery address not found.']
            ]);
        }
    }

}
