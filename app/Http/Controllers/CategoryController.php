<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /****************************************************************************************************
     * Display a listing of store's categories
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $store = $request->mddlwr_store;
        return response([
            'categories' => $store->categories
        ]);
    }


    /****************************************************************************************************
     *  Store a newly created category.
     *
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'string|required|max:50',
            'description' => 'string|max:300|nullable'
        ]);

        $store = $request->mddlwr_store;
        $category_name = Str::upper($request->name);
        if($store->categories->where('name', $category_name)->first() === null) {
            $category = new Category();
            $category->store_id    = $store->id;
            $category->name        = $category_name;
            $category->description = isset($request->description) ? $request->description : '';
            $category->save();

            return response([
                'stored' => true
            ]);
        }
        else {
            throw ValidationException::withMessages([
                'name' => ['New category name already exists in this store.']
            ]);
        }
    }


    /****************************************************************************************************
     * Fetch data for editing a category.
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        return response([
            'category' => $request->mddlwr_category
        ]);
    }


    /****************************************************************************************************
     * Update the specified category.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'string|required|max:50',
            'description' => 'string|max:300|nullable'
        ]);

        $store    = $request->mddlwr_store;
        $category = $request->mddlwr_category;
        $category_name = Str::upper($request->name);
        if($store->categories->where('id', '!=', $id)->where('name', $category_name)->first() === null) {
            $category->name        = $category_name;
            $category->description = isset($request->description) ? $request->description : '';
            $category->update();
            return response([
                'updated' => true
            ]);
        }
        else {
            throw ValidationException::withMessages([
                'name' => ['Updated category name already exists in this store.']
            ]);
        }
    }


    /****************************************************************************************************
     * Remove the specified category.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function destroy(Request $request, $id)
    {
        $store = $request->mddlwr_store;
        if($category = $store->categories->find($id)) {
            $category->delete();
            return response([
                'deleted' => true
            ]);
        }
        else {
            throw ValidationException::withMessages([
                'category' => ['Category not found.']
            ]);
        }
    }
}
