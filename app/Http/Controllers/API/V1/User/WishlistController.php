<?php

namespace App\Http\Controllers\API\V1\User;
use App\Http\Controllers\Controller;

use App\Models\Product;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(){
        $items=Cart::instance('wishlist')->content();
        return response()->json([
            'message' => 'success',
            'wishlist' => $items
        ]);
    }
    public function addTOWishlist(Request $request){
        cart::instance('wishlist')->add($request->id,$request->name,$request->quantity,$request->price)
        ->associate(Product::class);
        return response()->json([
            'message' => 'Item added to wishlist successfully',
            'wishlist' => Cart::instance('wishlist')->content()
        ], 201);
    }


    public function removeItem($rowId){
        Cart::instance('wishlist')->remove($rowId);
        return response()->json([
            'message' => 'Item removed from wishlist',
            'wishlist' => Cart::instance('wishlist')->content()
        ]);
    }

    public function clearWishList(){
        Cart::instance('wishlist')->destroy();
        return response()->json([
            'message' => 'Wishlist cleared successfully',
            'wishlist' => []
        ]);
    }

    public function moveToCart($rowId){
        $item=Cart::instance('wishlist')->get($rowId);
        if (!$item) {
            return response()->json([
                'message' => 'Item not found in wishlist'
            ], 404);
        }
        Cart::instance('wishlist')->remove($rowId);
        Cart::instance('cart')->add($item->id,$item->name,$item->qty,$item->price)->associate(Product::class);
        return response()->json([
            'message' => 'Item moved to cart successfully',
            'cart' => Cart::instance('cart')->content(),
            'wishlist' => Cart::instance('wishlist')->content()
        ]);
    }
}
