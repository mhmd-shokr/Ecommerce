<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index(){
            $items=Cart::instance('cart')->content();
            return view('cart', compact('items'));
    }
    public function addToCart(Request $request)
    {
        Cart::instance('cart')->add(
            $request->id,
            $request->name,
            $request->quantity,
            $request->price
        )->associate(Product::class); 
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }
    
    public function increaseCartQuantity($rowId)
    {
            $product=Cart::instance('cart')->get($rowId);
            $qty=$product->qty+1;
            Cart::instance('cart')->update($rowId,$qty);
            return redirect()->back();
    }

    public function decreaseCartQuantity($rowId)
    {
            $product=Cart::instance('cart')->get($rowId);
            $qty=$product->qty-1;
            Cart::instance('cart')->update($rowId,$qty);
            return redirect()->back();
    }

    public function removeItem($rowId){
        $product=Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function clearCart()
{
    Cart::instance('cart')->destroy();

    return redirect()->back();
}
}
