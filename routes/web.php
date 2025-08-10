<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;

use App\Http\Controllers\SocialController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;






Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop',[ShopController::class,'index'])->name('shop.index');
Route::get('/shop/{product_slug}',[ShopController::class,'productDetails'])->name('shop.product.details');

Route::get('/cart',[CartController::class,'index'])->name('cart.index');
Route::post('/cart/add',[CartController::class,'addToCart'])->name('cart.add');

Route::post('/wishlist/add',[WishlistController::class,'addTOWishlist'])->name('wishlist.add');
Route::get('/wishlist',[WishlistController::class,'index'])->name('wishlist.index');
Route::delete('/wishlist/remove/{rowId}',[WishlistController::class,'removeItem'])->name('wishlist.removeItem');
Route::delete('/wishlist/clear',[WishlistController::class,'clearWishList'])->name('wishlist.clear');
Route::post('/wishlist/moveToCart/{rowId}',[WishlistController::class,'moveToCart'])->name('wishlist.moveToCart');


Route::put('/cart/increaseQuantity/{rowId}',[CartController::class,'increaseCartQuantity'])->name('cart.qty.increase');
Route::put('/cart/decreaseQuantity/{rowId}',[CartController::class,'decreaseCartQuantity'])->name('cart.qty.decrease');
Route::delete('/cart/remove/{rowId}',[CartController::class,'removeItem'])->name('cart.removeItem');
Route::delete('/cart/clearCart',[CartController::class,'clearCart'])->name('cart.clearCart');
Route::post('/cart/applyCoupon',[CartController::class,'applyCoupon'])->name('cart.addCoupons');
Route::delete('/cart/removeCoupon',[CartController::class,'removeCoupon'])->name('cart.removeCoupon');
Route::get('/checkout',[CartController::class,'checkout'])->name('cart.checkout');
Route::post('/placeAnOrder',[CartController::class,'placeAnOrder'])->name('cart.placeAnOrder');
Route::get('/orderConfirmation',[CartController::class,'orderConfirmation'])->name('cart.orderConfirmation');

Route::middleware(['auth'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/orders',[UserController::class,'orders'])->name('users.orders');
    Route::get('/user/orders/{id}/details',[UserController::class,'orderDetails'])->name('users.orders.details');

});

Route::controller(SocialController::class)->group(function(){
    Route::get('/auth/redirect/{parameter}','redirect')->name('auth.redirect');
    Route::get('/auth/callBack/{parameter}','callBack')->name('auth.callBack');
});

Route::get('/complete-profile', [ProfileController::class, 'showCompleteForm'])->name('profile.complete');
Route::post('/complete-profile', [ProfileController::class, 'saveCompleteForm'])->name('profile.complete.save');


Route::middleware(['auth','verified', AuthAdmin::class])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/brands',[AdminController::class, 'Brands'])->name('admin.brands');
    Route::get('/admin/addBrand',[AdminController::class,'addBrand'])->name('admin.addBrand');
    Route::post('/admin/brand/store',[AdminController::class, 'brandStore'])->name('admin.store');
    Route::get('/admin/brand/{id}/edit',[AdminController::class, 'brandEdit'])->name('admin.edit');
    Route::put('/admin/brand/update',[AdminController::class, 'brandUpdate'])->name('admin.update');
    Route::delete('/admin/brand/{id}/delete',[AdminController::class, 'brandDelete'])->name('admin.delete');

    Route::get('/admin/categories',[AdminController::class,'categories'])->name('admin.categories');
    Route::get('/admin/addCategories',[AdminController::class,'category_add'])->name('admin.addCategory');
    Route::post('/admin/storeCategories',[AdminController::class,'categoryStore'])->name('admin.categoryStore');
    Route::get('/admin/brand/{id}/edit',[AdminController::class, 'categoryEdit'])->name('admin.categoryEdit');
    Route::put('/admin/brand/update',[AdminController::class, 'categoryUpdate'])->name('admin.categoryUpdate');
    Route::delete('/admin/category/{id}/delete',[AdminController::class, 'categoryDelete'])->name('admin.categoryDelete');

    Route::get('/admin/products',[AdminController::class,'products'])->name('admin.products');
    Route::get('/admin/addProducts',[AdminController::class,'addProduct'])->name('admin.addProducts');
    Route::post('/admin/storeProducts',[AdminController::class,'productStore'])->name('admin.storeProducts');
    Route::get('/admin/product/{id}/edit',[AdminController::class,'editProduct'])->name('admin.editProduct');
    Route::put('/admin/product/update',[AdminController::class,'updateProduct'])->name('admin.updateProduct');
    Route::delete('/admin/product/{id}/delete',[AdminController::class, 'deleteProduct'])->name('admin.deleteProduct');

    Route::get('/admin/coupons',[AdminController::class,'coupons'])->name('admin.coupons');
    Route::get('/admin/addCoupons',[AdminController::class,'addCoupons'])->name('admin.addCoupons');
    Route::post('/admin/coupons/store', [AdminController::class, 'storeCoupons'])->name('admin.coupons.store');
    Route::get('/admin/coupons/{id}/edit', [AdminController::class, 'editCoupons'])->name('admin.coupons.edit');
    Route::put('/admin/coupons/{id}/update', [AdminController::class, 'updateCoupons'])->name('admin.coupons.update');
    Route::delete('/admin/coupons/{id}/delete', [AdminController::class, 'deleteCoupons'])->name('admin.coupons.delete');
    
    Route::get('/admin/orders',[AdminController::class,'orders'])->name('admin.orders');
    Route::get('/admin/oredr/{id}/details',[AdminController::class,'orderDetails'])->name('admin.order.details');
    Route::put('/admin/oredr/update/status',[AdminController::class,'updateOrderStatus'])->name('admin.update.status');

});
