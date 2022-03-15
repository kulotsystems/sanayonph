<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AgentController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PhAddr\RegionController;
use App\Http\Controllers\PhAddr\ProvinceController;
use App\Http\Controllers\PhAddr\MuncityController;
use App\Http\Controllers\PhAddr\BarangayController;
use App\Http\Controllers\DeliveryAddressController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ExploreController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// GROUP :: AGENT
Route::group(['prefix' => 'agent'], function() {
    Route::post('/payments-to-confirm'    , [AgentController::class, 'payments_to_confirm']);
    Route::post('/confirm-decline-payment', [AgentController::class, 'confirm_decline_payment']);
    Route::post('/payments-to-transfer'   , [AgentController::class, 'payments_to_transfer']);
});


// GROUP :: AUTHENTICATION
Route::group(['prefix' => 'auth'], function() {
    Route::group(['prefix' => 'sign-up'], function() {
        Route::post('/otp-send'  , [SignUpController::class, 'send_otp']);
        Route::post('/otp-verify', [SignUpController::class, 'verify_otp']);
    });

    Route::group(['prefix' => 'sign-in'], function() {
        Route::post('/'   , [SignInController::class, 'sign_in']);
        Route::get('/user', [SignInController::class, 'get_user']);
    });

    Route::group(['prefix' => 'reset'], function() {
        Route::post('/check-username' , [ResetPasswordController::class, 'check_username']);
        Route::post('/otp-send'       , [ResetPasswordController::class, 'send_otp']);
        Route::post('/otp-verify'     , [ResetPasswordController::class, 'verify_otp']);
        Route::post('/change-password', [ResetPasswordController::class, 'change_password']);
    });
});


// GROUP :: PH ADDRESS
Route::group(['prefix' => 'phaddr'], function() {
    Route::get('/regions'  , [RegionController::class  , 'index']);
    Route::get('/provinces', [ProvinceController::class, 'index']);
    Route::get('/muncities', [MuncityController::class , 'index']);
    Route::get('/barangays', [BarangayController::class, 'index']);
});


// GROUP :: STORE
Route::group(['prefix' => 'store'], function() {
    Route::middleware(['verify-username'])->group(function() {
        Route::get('/', [StoreController::class, 'user']);
        Route::middleware(['verify-store'])->group(function() {
            Route::get('/products',      [StoreController::class, 'products']);
            Route::middleware(['verify-product', 'verify-product-published'])->group(function() {
                Route::get('/products/{id}/{action}', [StoreController::class, 'product'])->where('id', '[0-9]+');
            });
        });
        Route::middleware(['verify-user-self'])->group(function() {
            Route::get('/merchant-code', [StoreController::class, 'merchant_code']);
        });
    });
});


// GROUP :: SHOP
Route::group(['prefix' => 'shop'], function() {
    Route::get('/stores', [StoreController::class, 'stores']);
});

// GROUP :: EXPLORE
Route::group(['prefix' => 'explore'], function() {
    Route::get('/', [ExploreController::class, 'index']);
});

// MIDDLEWARE :: Auth Sanctum
Route::middleware(['auth:sanctum'])->group(function() {

    // GROUP :: USER
    Route::group(['prefix' => 'user'], function() {
        Route::middleware(['verify-user-self'])->group(function() {
            Route::get   ('/'        , [UserController::class, 'get_user']);
            Route::delete('/'        , [UserController::class, 'sign_out']);
            Route::post  ('/personal', [UserController::class, 'update_personal']);
            Route::post  ('/account' , [UserController::class, 'update_account']);
            Route::post  ('/avatar'  , [UserController::class, 'update_avatar']);
        });
    });


    // MIDDLEWARE :: Verify Username
    Route::middleware(['verify-username'])->group(function() {

        // GROUP :: DELIVERY ADDRESS
        Route::group(['prefix' => 'delivery-addr'], function() {
            Route::middleware(['verify-user-self'])->group(function() {
                Route::get   ('/'    , [DeliveryAddressController::class, 'index']);
                Route::post  ('/'    , [DeliveryAddressController::class, 'store']);
                Route::delete('/{id}', [DeliveryAddressController::class, 'destroy'])->where('id', '[0-9]+');
                Route::middleware(['verify-user-self'])->group(function() {
                    Route::middleware(['verify-delivery-address'])->group(function() {
                        Route::get   ('/edit/{id}', [DeliveryAddressController::class, 'edit'])->where('id', '[0-9]+');
                        Route::put   ('/{id}'     , [DeliveryAddressController::class, 'update'])->where('id', '[0-9]+');
                    });
                });
            });
        });

        // GROUP :: ORDER
        Route::group(['prefix' => 'order'], function() {
            Route::middleware(['verify-store'])->group(function() {
                // Buyer Order
                Route::middleware(['verify-product', 'verify-product-published'])->group(function() {
                    Route::post('/place/{id}', [OrderController::class, 'place_order'])->where('id', '[0-9]+');
                });

                // Seller Order
                Route::middleware(['verify-user-self'])->group(function() {
                    Route::get('/seller', [OrderController::class, 'seller_index']);
                    Route::middleware(['verify-order'])->group(function() {
                        Route::get ('/seller/{id}', [OrderController::class, 'seller_show'])->where('id', '[0-9]+');
                        Route::put ('/seller/{id}', [OrderController::class, 'seller_decline'])->where('id', '[0-9]+');
                        Route::post('/seller/{id}', [OrderController::class, 'seller_confirm'])->where('id', '[0-9]+');
                    });
                });
            });

            // Buyer Order
            Route::middleware(['verify-user-self'])->group(function() {
                Route::get('/', [OrderController::class, 'buyer_index']);
                Route::middleware(['verify-order'])->group(function() {
                    Route::get ('/{id}',            [OrderController::class, 'buyer_show'])->where('id', '[0-9]+');
                    Route::put ('/{id}',            [OrderController::class, 'buyer_cancel'])->where('id', '[0-9]+');
                    Route::post('/{id}',            [OrderController::class, 'buyer_receive'])->where('id', '[0-9]+');
                    Route::post('/{id}/screenshot', [OrderController::class, 'buyer_screenshot'])->where('id', '[0-9]+');
                });
            });
        });

        // MIDDLEWARE :: Verify Store
        Route::middleware(['verify-store'])->group(function() {

            // GROUP :: CATEGORY
            Route::group(['prefix' => 'category'], function() {
                Route::get('/', [CategoryController::class, 'index']);
                Route::middleware(['verify-user-self'])->group(function() {
                    Route::get   ('/list', [CategoryController::class, 'index']);
                    Route::post  ('/'    , [CategoryController::class, 'store']);
                    Route::delete('/{id}', [CategoryController::class, 'destroy'])->where('id', '[0-9]+');
                    Route::middleware(['verify-category'])->group(function() {
                        Route::get   ('/edit/{id}', [CategoryController::class, 'edit'])->where('id', '[0-9]+');
                        Route::put   ('/{id}'     , [CategoryController::class, 'update'])->where('id', '[0-9]+');
                    });
                });
            });

            // GROUP :: PRODUCT
            Route::group(['prefix' => 'product'], function() {
                Route::get   ('/', [ProductController::class, 'index']);
                Route::middleware(['verify-user-self'])->group(function() {
                    Route::get   ('/list'        , [ProductController::class, 'index']);
                    Route::post  ('/'            , [ProductController::class, 'store']);
                    Route::delete('/{id}'        , [ProductController::class, 'destroy'])->where('id', '[0-9]+');
                    Route::post  ('/photo'       , [ProductController::class, 'upload_photo']);
                    Route::put   ('/publish/{id}', [ProductController::class, 'publish'])->where('id', '[0-9]+');
                    Route::get   ('/dependencies', [ProductController::class, 'dependencies']);
                    Route::middleware(['verify-product'])->group(function() {
                        Route::get('/{id}'     , [ProductController::class, 'show'])->where('id', '[0-9]+');
                        Route::get('/edit/{id}', [ProductController::class, 'edit'])->where('id', '[0-9]+');
                        Route::put('/{id}'     , [ProductController::class, 'update'])->where('id', '[0-9]+');
                    });
                });
            });
        });
    });
});
