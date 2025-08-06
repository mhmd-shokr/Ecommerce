<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(){
        $products=Product::orderBy('created_at','desc')->paginate(5);
        return view('shop',compact('products'));
    }

    public function productDetails($product_slug){
        $product=Product::where('slug',$product_slug)->first();
        $products = Product::where('slug', '<>', $product_slug)
        ->latest()
        ->take(8)
        ->get();
        return view('details',compact('product','products'));
    }

    
}
