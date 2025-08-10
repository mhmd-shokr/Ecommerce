<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(){
        
        return view('users.index');
    }

    public function orders(){
        $orders=Order::where('user_id',Auth::id())->orderBy('created_at','desc')->paginate(10);
        return view('users.orders',compact('orders'));
    }

    public function orderDetails($orderId)
    {
        $order=Order::where('user_id',Auth::id())->where('id',$orderId)->first();
        if($order)
            {
                $orderItems=OrderItem::where('order_id',$orderId)->orderBy('id')->paginate(12);
                $transaction=Transaction::where('order_id',$orderId)->first();
                return view('users.ordersDetails',compact('order','orderItems','transaction'));
            }    
        else{
            return redirect()->route('login');
        }       

    }
}
