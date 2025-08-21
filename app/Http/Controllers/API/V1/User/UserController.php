<?php

namespace App\Http\Controllers\API\V1\User;
use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(){
        
        $user = Auth::user();

        return response()->json([
            'message' => 'success',
            'user' => $user
        ]);    }

    public function orders(){
        $orders=Order::where('user_id',Auth::id())->orderBy('created_at','desc')->paginate(10);
        return response()->json([
            'message' => 'success',
            'orders' => $orders
        ]);
    }

    public function orderDetails($orderId)
    {
        $order=Order::where('user_id',Auth::id())->where('id',$orderId)->first();
        if (!$order) {
            return response()->json([
                'message' => 'Order not found or not authorized'
            ], 404);
        }
        
        $orderItems = OrderItem::where('order_id', $orderId)
        ->orderBy('id')
        ->paginate(12);

        $transaction = Transaction::where('order_id', $orderId)->first();

        return response()->json([
            'message' => 'success',
            'order' => $order,
            'items' => $orderItems,
            'transaction' => $transaction
        ]);
    }

    public function cancelOredr(Request $request)
    {
        $order=Order::findOrFail($request->order_id);
        
        if (!$order) {
            return response()->json([
                'message' => 'Order not found or not authorized'
            ], 404);
        }
        $order->status ='canceled';
        $order->canceled_date=Carbon::now();
        $order->save();

        return response()->json([
            'message' => 'Order canceled successfully',
            'order' => $order
        ]);
    }
}
