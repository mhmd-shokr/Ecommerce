<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Number of products per page (default is 12 if not provided in URL)
        $size = $request->query('size') ?? 12;

        // Order value from the URL (default is -1 meaning default order)
        $order = $request->query('order') ?? -1;
    
        // Filtered brands from the URL (like '1,2,3')
        $fBrands = $request->query('brands');

        // Filtered Categories from the URL (like '1,2,3')
        $fCategories=$request->query('categories');

        $minPrice=$request->query('min') ?? 1 ;
        $maxPrice=$request->query('max') ?? 100000;

        $maxRegularPrice = Product::max('regular_price');
        $maxSalePrice = Product::max('sale_price');
    
        $maxSliderPrice = max($maxRegularPrice, $maxSalePrice);
        $minSliderPrice = product::min('sale_price');
        //  Variables to store the ordering column and direction
        $oColumn = "";
        $oOrder = "";

        //  Choose the correct column and order direction based on the value of $order
        switch ($order) {
            case 1: // Newest first
                $oColumn = 'created_at';
                $oOrder = 'desc';
                break;
            case 2: // Oldest first
                $oColumn = 'created_at';
                $oOrder = 'asc';
                break;
            case 3: // Price low to high
                $oColumn = 'regular_price';
                $oOrder = 'asc';
                break;
            case 4: // Price high to low
                $oColumn = 'regular_price';
                $oOrder = 'desc';
                break;
            default: // Default: order by ID descending
                $oColumn = 'id';
                $oOrder = 'desc';
        }
    
        //  Get all available brands (used in filter section in the view)
        $brands = Brand::orderBy('name', 'asc')->get();

        //  Get all available categories (used in filter section in the view)
        $categories = Category::orderBy('name', 'asc')->get();

         //  Get all available categories (used in filter section in the view)
            $prices = Product::orderBy('name', 'asc')->get();
    
        //  Build the query to get products
        // use ($fBrands) beacuse that out of function
        $products = Product::where(function ($query) use ($fBrands, $fCategories,$minPrice,$maxPrice) {
            //  If there are selected brands from the filter
            if ($fBrands) {
                // Split the comma-separated values into an array and filter by brand_id
                $query->whereIn('brand_id', explode(',', $fBrands));
            }

            if ($fCategories) {
                $query->whereIn('category_id', explode(',', $fCategories));
            }

            if ($minPrice && $maxPrice ) {
                $query->where(function($q) use($minPrice,$maxPrice){
                    $q->whereBetween('regular_price',[$minPrice,$maxPrice])
                    ->orWhereBetween('sale_price',[$minPrice,$maxPrice]);
                });
            }
        })
        //  Order the products by the selected column and direction
        ->orderBy($oColumn, $oOrder)
    
        //  Paginate the results by the selected size
        ->paginate($size);
    
        return view('shop', compact('products', 'size', 'order', 'brands', 'fBrands','categories','fCategories','minPrice','maxPrice','maxSliderPrice','minSliderPrice'));
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
