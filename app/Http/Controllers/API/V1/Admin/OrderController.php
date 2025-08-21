<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(){
        $orders=Order::orderBy('created_at','desc')->paginate();
        return response()->json([
            "data"=>$orders,
            "message"=>'succes',
        ]);
    }

    public function show(Order $order){
        $orderitems=OrderItem::where('order_id',$order->id)->orderBy('id')->paginate();
        $transaction=Transaction::where('order_id',$order->id)->first();
        return response()->json([
            "message"=>'success',
            "order"=>$order,
            "orderitems"=>$orderitems,
            "transaction"=>$transaction,
        ]);
    }

    public function update(Request $request,Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:ordered,delivered,canceled'
        ]);
        
        //change status debendon user request delivered or canceled or canceled
        $order->status = $request->order_status;
    
        if ($request->order_status == 'delivered') {
            $order->delivered_date = Carbon::now();
            
        } elseif ($request->order_status == 'canceled') {
            $order->canceled_date = Carbon::now();
        }
    
        $order->save();
    
        if ($request->order_status == 'delivered') {
            $transaction = Transaction::where('order_id', $order->id)->first();
            if ($transaction) {
                $transaction->status = 'approved';
                $transaction->save();
            }
        }
    
        return response()->json([
            "message"=>'Status changed successfully',
            "data"=>$order,
        ]);
    }

}
