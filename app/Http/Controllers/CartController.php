<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
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
        return redirect()->back()->with('success', 'Product added to cart successfully');
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

    public function applyCoupon(Request $request)
    {
        //get code from form
        $couponCode = $request->coupon_code;
    //isset to cofirm it not null and search coupon in DB
        if (isset($couponCode)) {
            $coupon = Coupon::where('code', $couponCode)
                ->where('expiry_date', '>=', Carbon::today())
                ->where('cart_value', '<=', Cart::instance('cart')->subtotal())
                ->first();
            if (!$coupon) {
                return redirect()->back()->with('error', 'Invalid coupon code');
            } else {
                Session::put('coupon', [
                    'code'       => $coupon->code,
                    'type'       => $coupon->type,
                    'value'      => $coupon->value,
                    'cart_value' => $coupon->cart_value,
                ]);
                $this->calculateDiscount();
                return redirect()->back()->with('success', 'Coupon applied successfully');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid coupon code');
        }
    }

    public function calculateDiscount(){
        $discount= 0.0 ;
        if(Session::has('coupon')){        
            if(Session::get('coupon')['type'] == 'fixed'){
                $discount = Session::get('coupon')['value'];
            }else{
                /**
                     *precentage of coupon * cart.subtotal()
                 */
                $discount = Cart::instance('cart')->subtotal()*Session::get('coupon')['value'] / 100;
            }
            //remove discount from cart.subtotal()
            $subTotalAfterDiscount=Cart::instance('cart')->subtotal() - $discount;
            //calc tax from subtotal after discount tax
            $taxAfterDiscount=($subTotalAfterDiscount* config('cart.tax'))/100;

            $totalAfterDiscount=$subTotalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts',[
                'discount' => number_format(floatval($discount),2,'.',''),
                'subtotal' => number_format(floatval($subTotalAfterDiscount),2,'.',''),
                'tax' => number_format(floatval($taxAfterDiscount),2,'.',''),
                'total' => number_format(floatval($totalAfterDiscount),2,'.',''),
            ]);
        }
        
    }
    
}
