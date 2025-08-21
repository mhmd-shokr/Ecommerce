<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index(){
        $products=Product::with(['category','brand'])->orderBy('created_at','desc')->paginate(10);
        return response()->json([
            "data"=>ProductResource::collection($products),
            "message"=>'success',
        ],200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug',
            'short_description' => 'nullable|string',
            'description' => 'required|string',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:regular_price',
            'SKU' => 'required|string|unique:products,SKU',
            'stock_status' => 'required|in:instock,outofstock',
            'featured' => 'nullable|boolean',
            'quantity' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);
    
        $product = new Product();
        $product->name = $request->name;
        $product->slug = trim(Str::slug($request->slug));
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = Str::upper(Str::slug($request->SKU, '-'));
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured ?? false;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
    
        $current_timeTemp = Carbon::now()->timestamp;
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $current_timeTemp . '_main.' . $image->extension();
            $this->GenerateProductThumbalisImage($image, $imageName);
            $product->image = $imageName;
        }
    
        $gallary_arr = [];
        $counter = 1;
    
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                if (in_array($gextension, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $gfileName = $current_timeTemp . '_' . $counter . '.' . $gextension;
                    $this->GenerateProductThumbalisImage($file, $gfileName);
                    $gallary_arr[] = $gfileName;
                    $counter++;
                }
            }
        }
    
        $product->images = implode(',', $gallary_arr);
        $product->save();
    
        return response()->json([
            "data"=>$product,
            "message"=>'success',
        ],201);
    }
    
    public function GenerateProductThumbalisImage($file, $filename)
    {
        $path = storage_path('app/public/products/thumbnails');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    
        $img = Image::make($file->getRealPath());
        $img->save($path . '/' . $filename);
    }
    
    public function update( Request $request,Product $product)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:products,slug,'.$product->id.'id',
            'short_description' => 'sometimes|string',
            'description' => 'sometimes|string',
            'regular_price' => 'sometimes|numeric|min:0',
            'sale_price' => 'sometimes|numeric|min:0|lt:regular_price',
            'SKU' => 'sometimes|string|unique:products,SKU,'.$product->id.'id',
            'stock_status' => 'sometimes|in:instock,outofstock',
            'featured' => 'sometimes|boolean',
            'quantity' => 'sometimes|integer|min:0',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:2048',
            'category_id' => 'sometimes|exists:categories,id',
            'brand_id' => 'sometimes|exists:brands,id',
        ]);

        $product->name = $request->name;
        $product->slug = trim(Str::slug($request->slug));
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = Str::upper(Str::slug($request->SKU, '-'));
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured ?? false;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
    
        $current_timeTemp = Carbon::now()->timestamp;
    
        if ($request->hasFile('image')) {
            if($product->image && File::exists(storage_path('app/public/products/thumbnails/'.$product->image))){
                File::delete(storage_path('app/public/products/thumbnails/'.$product->image));
            }
        
            $image = $request->file('image');
            $imageName = $current_timeTemp . '_main.' . $image->extension();
            $this->GenerateProductThumbalisImage($image, $imageName);
            $product->image = $imageName;
        }
        
        $gallary_arr = [];
        $counter = 1;
        
        if ($request->hasFile('images')) {
            // احذف الصور القديمة
            foreach(explode(',', $product->images) as $ofile) {
                if($ofile && File::exists(storage_path('app/public/products/thumbnails/'.$ofile))){
                    File::delete(storage_path('app/public/products/thumbnails/'.$ofile));
                }
            }
        
            $files = $request->file('images');
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                if (in_array($gextension, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $gfileName = $current_timeTemp . '_' . $counter . '.' . $gextension;
                    $this->GenerateProductThumbalisImage($file, $gfileName);
                    $gallary_arr[] = $gfileName;
                    $counter++;
                }
            }
        }
        
        $product->images = implode(',', $gallary_arr);
        
        $product->save();
        return response()->json([
            "data"=>$product,
            "message"=>'success updated',
        ],200);
    }
    public function destroy(Product $product){

            if(File::exists(public_path('uploads/products/').'/'.$product->image)){
                File::delete(public_path('uploads/products/').'/'.$product->image);
            }
            if(File::exists(public_path('uploads/products/thumbnails/').'/'.$product->image)){
                File::delete(public_path('uploads/products/thumbnails/').'/'.$product->image);
            }

            foreach(explode(',',$product->images)as $ofile )
            {
                if(File::exists(public_path('uploads/products/').'/'.$ofile)){
                    File::delete(public_path('uploads/products/').'/'.$ofile);
                }
                if(File::exists(public_path('uploads/products/thumbnails/').'/'.$ofile)){
                    File::delete(public_path('uploads/products/thumbnails/').'/'.$ofile);
                }
            }
        $product->delete();
            return response()->json([
                "message"=>'succProduct deleted successfullyess',
            ],200);
        }
}
