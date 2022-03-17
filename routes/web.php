<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/app');
});

Route::get('app/{vue_capture?}', function () {
    return view('sanayonph.index');
})->where('vue_capture', '[\/\w\.-]*');

Route::get('/about', function () {
    return view('sanayonph.about');
});

Route::get('demo', [\App\Http\Controllers\DemoController::class, 'index']);


/*
|--------------------------------------------------------------------------
| UTILITY ROUTES
|--------------------------------------------------------------------------
|
*/
Route::get('/xxxx', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return "CACHE IS CLEARED!";
});

Route::get('/yyyy', function() {
    Artisan::call('storage:link');
    return "STORAGE LINKED!";
});
