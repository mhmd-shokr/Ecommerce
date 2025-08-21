<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponsController extends Controller
{
    public function index(){
        $coupons=Coupon::orderBy('expiry_date','desc')->paginate(12);
        return response()->json([
            "message"=>'success',
            "data"=>$coupons
        ],200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => ['required', Rule::in(['fixed', 'percent'])],
            'value' => 'required|numeric|min:0',
            'cart_value' => 'required|numeric|min:0',
            'expiry_date' => 'required|date|after_or_equal:today',
        ]);
    $data= Coupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'cart_value' => $request->cart_value,
            'expiry_date' => $request->expiry_date,
        ]);
        return response()->json([
            "message"=>'Coupons Created Successfully',
            "data"=>$data
        ],201);
    }

    public function update(Request $request,Coupon $coupon){
        $request->validate([
            'code' => 'sometimes|unique:coupons,code,' . $coupon->id,
            'type' => ['sometimes', Rule::in(['fixed', 'percent'])],
            'value' => 'sometimes|numeric|min:0',
            'cart_value' => 'sometimes|numeric|min:0',
            'expiry_date' => 'sometimes|date|after_or_equal:today',
        ]);
        $data=$coupon->update([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'cart_value' => $request->cart_value,
            'expiry_date' => $request->expiry_date,
        ]);
        return response()->json([
            "message"=>'Coupons updated Successfully',
            "data"=>$data
        ],200);    }

    public function destroy($id){
        $coupon = Coupon::findOrFail($id);
            $coupon->delete(); return response()->json([
                "message"=>'Coupons Deleted Successfully',
            ],200);  
    }
}
