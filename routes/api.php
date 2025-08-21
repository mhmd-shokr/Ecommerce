<?php

use App\Http\Controllers\API\V1\Admin\BrandsController;
use App\Http\Controllers\API\V1\Admin\CategoryController;
use App\Http\Controllers\API\V1\Admin\CouponsController;
use App\Http\Controllers\API\V1\Admin\DashboardController;
use App\Http\Controllers\API\V1\Admin\OrderController;
use App\Http\Controllers\API\V1\Admin\ProductController;
use App\Http\Controllers\API\V1\Admin\SlidesController;
use App\Http\Controllers\API\V1\User\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::prefix('admin')->group(function(): void{
    Route::apiResource('/dashboard',DashboardController::class);
    Route::apiResource('/products',ProductController::class);
    Route::apiResource('/categories',CategoryController::class);
    Route::apiResource('/coupons',CouponsController::class);
    Route::apiResource('/orders',OrderController::class);
    Route::apiResource('/slides',SlidesController::class);
    Route::apiResource('/brands',BrandsController::class);
});

Route::prefix('cart')->group(function(){
    Route::get('/',[CartController::class,'index']);
    Route::post('/add',[CartController::class,'addToCart']);
    Route::post('/increase/{rowId}',[CartController::class,'increaseCartQuantity']);
    Route::post('/decrease/{rowId}',[CartController::class,'decreaseCartQuantity']);
    Route::delete('/remove/{rowId}', [CartController::class, 'removeItem']);
    Route::delete('/clear', [CartController::class, 'clearCart']);
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon']);
    Route::delete('/remove-coupon', [CartController::class, 'removeCoupon']);
    Route::get('/checkout', [CartController::class, 'checkout']);
    Route::post('/place-order', [CartController::class, 'placeAnOrder']);
    Route::get('/order-confirmation', [CartController::class, 'orderConfirmation']);
});



























// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
