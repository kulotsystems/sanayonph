<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Image;

class UserController extends Controller
{
    /****************************************************************************************************
     * Updated User's Personal Info
     *
     * @param Request $request
     * @return Response
     */
    public function update_personal(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:64',
            'middle_name'   => 'string|max:64|nullable',
            'last_name'     => 'required|string|max:64',
            'name_suffix'   => 'string|max:32|nullable',
            'gender'        => 'required|string|in:Male,Female',
            'date_of_birth' => 'required|date|before:-15 years',
        ]);

        $user = Auth::user();
        $user->first_name    = Str::title($request->first_name);
        $user->middle_name   = Str::title($request->middle_name);
        $user->last_name     = Str::title($request->last_name);
        $user->name_suffix   = (Str::upper(Str::of($request->name_suffix)->substr(0, 1)) == 'I') ? Str::upper($request->name_suffix) : Str::title($request->name_suffix);
        $user->gender        = $request->gender == 'Male' ? 1 : 0;
        $user->date_of_birth = $request->date_of_birth;
        $user->update();

        return response([
            'user' => $user
        ]);
    }


    /****************************************************************************************************
     * Updated User's Account Info
     *
     * @param Request $request
     * @return Response
     */
    public function update_account(Request $request)
    {
        $request->validate([
            'gcash_name'        => 'required|string|max:128',
            'gcash_number'      => 'required|digits:11',
            'store_name'        => 'string|nullable',
            'store_description' => 'string|nullable|max:300|'
        ]);

        // update user account info
        $user = Auth::user();
        $user->gcash_name   = Str::upper($request->gcash_name);
        $user->gcash_number = $request->gcash_number;
        $user->update();

        // update store info
        $store = $user->store;
        $store->name        = $request->store_name;
        $store->description = $request->store_description;
        $store->update();

        return response([
            'user' => $user
        ]);
    }


    /****************************************************************************************************
     * Updated User's Avatar
     *
     * @param Request $request
     * @return Response
     */
    public function update_avatar(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // extract image from request
        $image = $request->file('file');

        // reference the user
        $user = Auth::user();

        // upload the image on /public/uploads/images/avatars/256
        $file_path = $image->storePubliclyAs('images/avatars/256', 'avtr-'. Str::kebab($user->last_name) . '-' . Str::kebab($user->first_name) . '-' . $user->id .'-'.time().'.png', ['disk' => 'uploads']);

        $file_name = basename($file_path);

        // make a thumbnail of the image
        $thumbnail = Image::make($image->getRealPath());

        // save 128 x 128 thumbnail
        $thumbnail->resize(128, 128, function($constraint) {
            $constraint->aspectRatio();
        })->save(public_path('uploads/images/avatars/128/') . $file_name);

        // save 64 x 64 thumbnail
        $thumbnail->resize(64, 64, function($constraint) {
            $constraint->aspectRatio();
        })->save(public_path('uploads/images/avatars/064/') . $file_name);

        // update user on the database
        $user = Auth::user();
        $user->avatar = $file_name;
        $user->update();

        // return response
        return response([
            'file_name' => $file_name
        ]);
    }


    /****************************************************************************************************
     * Sign out user
     *
     * @param Request $request
     * @return Response
     */
    public function sign_out(Request $request)
    {
        Auth::guard('web')->logout();
        return response([
            'signed_out' => true
        ]);
    }


    /****************************************************************************************************
     * Get public data
     *
     * @param Request $request
     * @return Response
     */
    public function get_public_data(Request $request)
    {

        return response([
            'user' => []
        ]);
    }
}
