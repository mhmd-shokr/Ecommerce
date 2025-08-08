<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(){
        $items=Cart::instance('wishlist')->content();
        return view('wishlist',compact('items'));
    }
    public function addTOWishlist(Request $request){
        cart::instance('wishlist')->add($request->id,$request->name,$request->quantity,$request->price)
        ->associate(Product::class);
        return redirect()->back();
    }


    public function removeItem($rowId){
        Cart::instance('wishlist')->remove($rowId);
        return redirect()->back();
    }

    public function clearWishList(){
        Cart::instance('wishlist')->destroy();
        return redirect()->back();
    }

    public function moveToCart($rowId){
        $item=Cart::instance('wishlist')->get($rowId);
        Cart::instance('wishlist')->remove($rowId);
        Cart::instance('cart')->add($item->id,$item->name,$item->qty,$item->price)->associate(Product::class);
        return redirect()->route('cart.index');

    }
}
