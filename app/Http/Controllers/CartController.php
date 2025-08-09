<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
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
        //check if session has coupon
        if(Session::has('coupon')){        
            if(Session::get('coupon')['type'] == 'fixed'){
                $discount = Session::get('coupon')['value'];
            }else{
                /**
                     *precentage of coupon * cart.subtotal()
                 */
                $discount = Cart::instance('cart')->subtotal() * Session::get('coupon')['value'] / 100;
            }
            //subtract discount from cart.subtotal()
            $subTotalAfterDiscount=Cart::instance('cart')->subtotal() - $discount;
            //calc tax from subtotal after discount tax
            $taxAfterDiscount=($subTotalAfterDiscount* config('cart.tax')) / 100;

            $totalAfterDiscount= $subTotalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts',[
                'discount' => number_format(floatval($discount),2,'.',''),
                'subtotal' => number_format(floatval($subTotalAfterDiscount),2,'.',''),
                'tax' => number_format(floatval($taxAfterDiscount),2,'.',''),
                'total' => number_format(floatval($totalAfterDiscount),2,'.',''),
            ]);
        }
        
    }
    public function removeCoupon(){
        Session::forget('coupon');
        Session::forget('discounts');
        return back()->with('success','Coupon has been removed');
    }

    public function checkout(){
        
        if(!Auth::check())
        {
            return redirect()->route('login');
        }
        $address=Address::where('user_id',Auth::user()->id)->where('isdefault',1)->first();
        return view('checkout',compact('address'));
    }    

    public function placeAnOrder(Request $request){
        $userId=Auth::id();
        $address=Address::where('user_id',$userId)->where('isdefault',true)->first();
        if(!$address){
        $validated=$request->validate([
                'name'=>'required|max:100',
                'phone'=>'required|numeric|digits:11',
                'zip'=>'required|numeric|digits:6',
                'state'=>'required',
                'city'=>'required',
                'address'=>'required',
                'locality'=>'required',
                'landmark'=>'required',
        ]);
        $validated['user_id'] = $userId;
        $validated['isdefault'] = true;
        $validated['country'] = 'Egypt';

        $address=Address::create( $validated);

        }

        $this->setAmountForCheckOut();

        $checkoutData = Session::get('checkout');
        if (!$checkoutData) {
            return redirect()->back()->withErrors('No checkout data found.');
        }
        
        $order=Order::create([
            'user_id' => $userId,
            'subtotal' => Session::get('checkout')['subtotal'],
            'discount' =>Session::get('checkout')['discount'],
            'tax' => Session::get('checkout')['tax'],
            'total' => Session::get('checkout')['total'],
            'name' => $address->name,
            'phone' => $address->phone,
            'locality' => $address->locality,
            'address' => $address->address,
            'city' => $address->city,
            'state' => $address->state,
            'country' => $address->country ?? 'Egypt',
            'landmark' => $address->landmark,
            'zip' => $address->zip,
            'type' => $address->type,
            'status' => 'ordered',
            'is_shipping_different' => false
        ]);

        foreach(Cart::instance('cart')->content() as $item){
            $order->orderItems()->create([
                'product_id' => $item->id,
                'order_id' => $order->id,
                'price' => $item->price,
                'quantity' => $item->qty
            ]);
        }

    if($request->mode=='card'){
            //
        }

    elseif($request->mode=='paypal'){
            //
        }


    elseif($request->mode=='cod'){
            $order->transaction()->create([
                'user_id' => $userId,
                'order_id' => $order->id,
                'mode' => 'cod',
                'status' => 'pending'
            ]);
        }
            
        Cart::instance('cart')->destroy();

        Session::forget(['checkout', 'coupon', 'discounts']);
        Session::put('order_id',$order->id);
        return redirect()->route('cart.orderConfirmation');
    }

    public function setAmountForCheckOut(){
        if(Cart::instance('cart')->content()->count() <= 0 ){
            Session::forget('checkout');
            return;
        }
    
        if(Session::has('coupon')){
            Session::put('checkout',[
                'discount' => (float) str_replace(',', '', Session::get('discounts')['discount']),
                'subtotal' => (float) str_replace(',', '', Session::get('discounts')['subtotal']),
                'tax' => (float) str_replace(',', '', Session::get('discounts')['tax']),
                'total' => (float) str_replace(',', '', Session::get('discounts')['total']),
            ]);
        }else{
            Session::put('checkout',[
                'discount' => 0,
                'subtotal' => (float) str_replace(',', '', Cart::instance('cart')->subtotal()),
                'tax' => (float) str_replace(',', '', Cart::instance('cart')->tax()),
                'total' => (float) str_replace(',', '', Cart::instance('cart')->total()),
            ]);
        }
    }
    


    public function orderConfirmation(){
        if(Session::has('order_id')){
            $order=Order::findOrFail(Session::get('order_id'));
            return view('orderConfirmation',compact('order'));
        }
        return redirect()->route('cart.index');

    }
}
