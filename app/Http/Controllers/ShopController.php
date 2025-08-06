<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request){

        $size= $request->query('size') ?? 12;
        $oColumn="";
        $oOrder="";
        $order= $request->query('order')?? -1;
        switch($order){
        case 1 :
            $oColumn='created_at';
            $oOrder='desc';
            break;
        case 2:
            $oColumn='created_at';
            $oOrder='asc';
            break;
        case 3:
                $oColumn='regular_price';
                $oOrder='asc';
                break;
        case 4:
                $oColumn='regular_price';
                $oOrder='desc';
                break;        
        default:
                $oColumn='id';
                $oOrder='desc';       
        }
        $products=Product::orderBy($oColumn,$oOrder)->paginate($size);
        return view('shop',compact('products','size','order'));
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
