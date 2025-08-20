<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Slide;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // Get last 3 orders sorted by creation date (latest first)
        $orders = Order::orderBy('created_at', 'desc')->take(3)->get();
        
        // Dashboard summary: total amounts and counts by status
        $dashboardData = Order::selectRaw("
            SUM(total) AS TotalAmount,
            SUM(IF(status='ordered', total, 0)) AS TotalOrderAmount,
            SUM(IF(status='delivered', total, 0)) AS TotalDeliveredAmount,
            SUM(IF(status='canceled', total, 0)) AS TotalCanceledAmount,
            COUNT(*) AS TotalOrders,
            SUM(IF(status='ordered', 1, 0)) AS OrderCount,
            SUM(IF(status='delivered', 1, 0)) AS DeliveredCount,
            SUM(IF(status='canceled', 1, 0)) AS CanceledCount
        ")->first();
        
        // Monthly data: join all months with orders data of the current year
        $monthlyDatas = DB::select("
            SELECT 
                M.id AS MonthNo,
                M.name AS MonthName,
                IFNULL(D.TotalAmount, 0) AS TotalAmount,
                IFNULL(D.TotalOrderAmount, 0) AS TotalOrderAmount,
                IFNULL(D.TotalDeliveredAmount, 0) AS TotalDeliveredAmount,
                IFNULL(D.TotalCanceledAmount, 0) AS TotalCanceledAmount
            FROM month_names M
    
            LEFT JOIN (
                SELECT 
                    MONTH(created_at) AS MonthNo,
                    DATE_FORMAT(created_at, '%b') AS MonthName,
                    SUM(total) AS TotalAmount,
                    SUM(IF(status = 'ordered', total, 0)) AS TotalOrderAmount,
                    SUM(IF(status = 'delivered', total, 0)) AS TotalDeliveredAmount,
                    SUM(IF(status = 'canceled', total, 0)) AS TotalCanceledAmount
                FROM orders
                WHERE YEAR(created_at) = YEAR(NOW())
                GROUP BY YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, '%b')
                ORDER BY MONTH(created_at)
            ) D ON D.MonthNo = M.id
        ");
    
        // Convert monthly amounts to comma-separated strings for chart usage
        $AmountM = implode(',', collect($monthlyDatas)->pluck('TotalAmount')->toArray());
        $orderedAmountM = implode(',', collect($monthlyDatas)->pluck('TotalOrderAmount')->toArray());
        $deliveredAmountM = implode(',', collect($monthlyDatas)->pluck('TotalDeliveredAmount')->toArray());
        $canceledAmountM = implode(',', collect($monthlyDatas)->pluck('TotalCanceledAmount')->toArray());
    
        // Calculate total sums for each type of amount across the year
        $totalAmount = collect($monthlyDatas)->sum('TotalAmount');
        $totalDeliveredAmount = collect($monthlyDatas)->sum('TotalDeliveredAmount');
        $totalOredredAmount = collect($monthlyDatas)->sum('TotalOrderAmount');
        $totalCanceledAmount = collect($monthlyDatas)->sum('TotalCanceledAmount');
    
        // Return data to the admin dashboard view
        return view(
            'admin.index', 
            compact(
                'orders', 
                'dashboardData',
                'AmountM',
                'orderedAmountM',
                'deliveredAmountM',
                'canceledAmountM',
                'totalAmount',
                'totalDeliveredAmount',
                'totalOredredAmount',
                'totalCanceledAmount'
            )
        );
    }
    
    

    public function Brands(){
        $brands=Brand::orderBy('id','desc')->paginate(10);
        return view('admin.brands',compact('brands'));
    }

    public function addBrand(){
        return view ('admin.addBrand');
    }

    public function brandStore(Request $request){
        $request->validate([
            'name'=>'required|string',
            'slug'=>'required|unique:brands,slug',
            'image'=>'nullable|mimes:png,jpg,jpeg|max:2048',
        ]);
        $imageName = null;
        if($request->hasFile('image')){
            //path of file will save in
            $destinationPath = public_path('uploads/brands');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            // save fileName without extension 
            $originalName = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
            //save extension 
            $extension = $request->image->getClientOriginalExtension();
            //compine fileName + extension
            $imageName = Str::slug($originalName) . '-' . time() . '.' . $extension;
            $image = Image::make($request->file('image')->getRealPath());
            $image->fit(300, 300, function ($constraint) {
                $constraint->upsize();
            });
                $image->save($destinationPath . '/' . $imageName);
        }

        $brand=Brand::create([
            'name'=>$request->name,
            'slug'=>Str::slug($request->slug),
            'image' => $imageName,
        ]);
        return redirect()->route('admin.brands')->with('success', 'Brand created successfully');
    }

    

        public function brandEdit($id){
            $brand=Brand::findOrFail($id);
            return view('admin.edit',compact('brand'));
        }
        public function brandUpdate(Request $request)
        {
            $brand=Brand::findOrFail($request->id);

            $request->validate([
                'name'=>'required|string',
                'slug'=>'required|unique:brands,slug,'.$brand->id,
                'image'=>'nullable|mimes:png,jpg,jpeg|max:2048',
            ]);
                $imageName = $brand->image;
                if($request->hasFile('image')){
                    //path of file will save in
                    $destinationPath = public_path('uploads/brands');
        
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    if ($imageName && file_exists($destinationPath . '/' . $imageName)) {
                        unlink($destinationPath . '/' . $imageName);
                    }
                    // save fileName without extension 
                    $originalName = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
                    //save extension 
                    $extension = $request->image->getClientOriginalExtension();
                    //compine fileName + extension
                    $imageName = Str::slug($originalName) . '.' . $extension;
                    //remove image from request to destinationPath
                    $request->image->move($destinationPath, $imageName);
                }
        
                $brand->update([
                    'name'=>$request->name,
                    'slug'=>Str::slug($request->slug),
                    'image' => $imageName,
                ]);
                return redirect()->back()->with('success', 'Brand updated successfully');
        }


        public function brandDelete($id){
            $brand=Brand::findOrFail($id);
            $destinationPath = public_path('uploads/brands/');
            $imagePath=$destinationPath.$brand->image;
            if (!empty($brand->image) && file_exists($imagePath) && is_file($imagePath)) {
                unlink($imagePath);
            }
            $brand->delete();
            return redirect()->back()->with('success', 'Brand deleted successfully');
        }


        public function categories(){
            $categories=Category::orderBy('id','desc')->paginate(10);

            return view('admin.categories',compact('categories'));
        }

        public function category_add(){
            return  view('admin.addCategory');
        }

        public function categoryStore(Request $request){
            $request->validate([
                'name'=>'required|string',
                'slug'=>'required|unique:categories,slug',
                'image'=>'nullable|mimes:png,jpg,jpeg|max:2048',
            ]);
            $imageName = null;
            if($request->hasFile('image')){
                //path of file will save in
                $destinationPath = public_path('uploads/categories');
    
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                // save fileName without extension 
                $originalName = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
                //save extension 
                $extension = $request->image->getClientOriginalExtension();
                //compine fileName + extension
                $imageName = Str::slug($originalName) . '-' . time() . '.' . $extension;
            
                $image = Image::make($request->file('image')->getRealPath());
                $image->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio(); 
                    $constraint->upsize();
                });
                $image->save($destinationPath . '/' . $imageName);
            }
    
            $category=Category::create([
                'name'=>$request->name,
                'slug'=>Str::slug($request->slug),
                'image' => $imageName,
            ]);
            return redirect()->route('admin.categories')->with('success', 'Category created successfully');
        }

        public function categoryEdit($id){
            $category=Category::findOrFail($id);
            return view('admin.categoryEdit',compact('category'));
        }
        public function categoryUpdate(Request $request){
            $request->validate([
                    'name'=>'required|string',
                    'slug'=>'required|unique:categories,slug,'.$request->id.',id',
                    'image'=>'nullable|mimes:png,jpg,jpeg|max:2048',
                ]);
                $category=Category::findOrFail($request->id);
                $imageName = $category->image;
                if($request->hasFile('image')){
                    //path of file will save in
                    $destinationPath = public_path('uploads/categories');
        
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    if ($imageName && file_exists($destinationPath . '/' . $imageName)) {
                        unlink($destinationPath . '/' . $imageName);
                    }
                    // save fileName without extension 
                    $originalName = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
                    //save extension 
                    $extension = $request->image->getClientOriginalExtension();
                    //compine fileName + extension
                    $imageName = Str::slug($originalName) . '.' . $extension;
                    $image = Image::make($request->file('image')->getRealPath());
                    $image->fit(300, 300, function ($constraint) {
                        $constraint->upsize();
                    });
                    $image->save($destinationPath . '/' . $imageName);
                }
                $category->update([
                    'name'=>$request->name,
                    'slug'=>Str::slug($request->slug),
                    'image' => $imageName,
                ]);
                return redirect()->route('admin.categories')->with('success', 'Categories updated successfully');
        }
        public function categoryDelete($id){
            $category=Category::findOrFail($id);
            $destinationPath = public_path('uploads/categories/');
            $imagePath=$destinationPath.$category->image;
            if (!empty($category->image) && file_exists($imagePath) && is_file($imagePath)) {
                unlink($imagePath);
            }
            $category->delete();
            return redirect()->back()->with('success', 'Category deleted successfully');
        }
        public function products(){
            $products=Product::orderBy('created_at','desc')->paginate(10);
            return view('admin.products',compact('products'));
        }

        public function addProduct(){
            $categories=Category::select('id','name')->orderBy('name')->get();
            $brands=Brand::select('id','name')->orderBy('name')->get();
            
            return view('admin.add-product',compact('categories','brands'));
        }

        public function productStore(Request $request)
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
        
            return redirect()->route('admin.products')->with('success', 'Product has been saved');
        }
        
        public function GenerateProductThumbalisImage($file, $filename)
        {
            $path = public_path('uploads/products/thumbnails');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
        
            $img = Image::make($file->getRealPath());
            $img->save($path . '/' . $filename);
        }
        
        
        public function editProduct($id){
            $product=Product::findOrFail($id);
            $categories=Category::select('id','name')->orderBy('name')->get();
            $brands=Brand::select('id','name')->orderBy('name')->get();
            return view ('admin.editProduct',compact('product','categories','brands'));
        }
        public function updateProduct( Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|unique:products,slug,'.$request->id.'id',
                'short_description' => 'nullable|string',
                'description' => 'required|string',
                'regular_price' => 'required|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0|lt:regular_price',
                'SKU' => 'required|string|unique:products,SKU,'.$request->id.'id',
                'stock_status' => 'required|in:instock,outofstock',
                'featured' => 'nullable|boolean',
                'quantity' => 'required|integer|min:0',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
            ]);

            $product=Product::find($request->id);
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
                if(File::exists(public_path('uploads/products/').'/'.$product->image)){
                    File::delete(public_path('uploads/products/').'/'.$product->image);
                }
                if(File::exists(public_path('uploads/products/thumbnails/').'/'.$product->image)){
                    File::delete(public_path('uploads/products/thumbnails/').'/'.$product->image);
                }
                $image = $request->file('image');
                $imageName = $current_timeTemp . '_main.' . $image->extension();
                $this->GenerateProductThumbalisImage($image, $imageName);
                $product->image = $imageName;
            }
            $gallary_arr = [];
            $counter = 1;
        
            if ($request->hasFile('images')) {
                foreach(explode(',',$product->images)as $ofile )
                if(File::exists(public_path('uploads/products/').'/'.$ofile)){
                    File::delete(public_path('uploads/products/').'/'.$ofile);
                }
                if(File::exists(public_path('uploads/products/thumbnails/').'/'.$ofile)){
                    File::delete(public_path('uploads/products/thumbnails/').'/'.$ofile);
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
            return redirect()->route('admin.products')->with('success', 'Product has been updated');
        }

        public function deleteProduct($id){
                $product=Product::findOrFail($id);

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
            return redirect()->route('admin.products')->with('success','Product deleted successfully');
    }


        public function coupons(){
            $coupons=Coupon::orderBy('expiry_date','desc')->paginate(12);
            return view('admin.coupons',compact('coupons'));
        }

        public function addCoupons()
        {
            return view('admin.addCoupons');
        }
    
        public function storeCoupons(Request $request)
        {
            $request->validate([
                'code' => 'required|unique:coupons,code',
                'type' => ['required', Rule::in(['fixed', 'percent'])],
                'value' => 'required|numeric|min:0',
                'cart_value' => 'required|numeric|min:0',
                'expiry_date' => 'required|date|after_or_equal:today',
            ]);
            Coupon::create([
                'code' => strtoupper($request->code),
                'type' => $request->type,
                'value' => $request->value,
                'cart_value' => $request->cart_value,
                'expiry_date' => $request->expiry_date,
            ]);
            return redirect()->route('admin.coupons')->with('success','Coupons Created Successfully');
        }

        public function editCoupons($id){
            $coupon=Coupon::findOrFail($id);
            return view('admin.editCoupons',compact('coupon'));
        }

        public function updateCoupons(Request $request){
            $request->validate([
                'code' => 'required|unique:coupons,code,' . $request->id,
                'type' => ['required', Rule::in(['fixed', 'percent'])],
                'value' => 'required|numeric|min:0',
                'cart_value' => 'required|numeric|min:0',
                'expiry_date' => 'required|date|after_or_equal:today',
            ]);
            $coupon = Coupon::findOrFail($request->id);
            $coupon->update([
                'code' => strtoupper($request->code),
                'type' => $request->type,
                'value' => $request->value,
                'cart_value' => $request->cart_value,
                'expiry_date' => $request->expiry_date,
            ]);
            return redirect()->route('admin.coupons')->with('success','Coupons updated Successfully');
        }

        public function deleteCoupons($id){
            $coupon = Coupon::findOrFail($id);
                $coupon->delete();
            return redirect()->route('admin.coupons')->with('success','Coupons Deleted Successfully');
        }

        public function orders(){
            $orders=Order::orderBy('created_at','desc')->paginate();
            return view ('admin.orders',compact('orders'));
        }

        public function orderDetails($orderId){
            $order=Order::findOrFail($orderId);
            $orderitems=OrderItem::where('order_id',$orderId)->orderBy('id')->paginate();
            $transaction=Transaction::where('order_id',$orderId)->first();
            return view('admin.orderDetails',compact('order','orderitems','transaction'));
        }

        public function updateOrderStatus(Request $request)
        {
            $order = Order::findOrFail($request->order_id);

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
        
            return back()->with('success', 'Status changed successfully');
        }


        public function slides(){
            $slides=Slide::orderBy('id','desc')->paginate(12);
            return view('admin.slides',compact('slides'));
        }


        public function addSlides(){
            return view('admin.addSlides');
        }

        public function storeSlide(Request $request){
            $request->validate([
                'tagline'=>'required|string|max:255',
                'title'=>'required|string',
                'subtitle'=>'required|string',
                'link'=>'required|url',
                'status'    => 'required|in:0,1',
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $slide=new Slide();
            $slide->tagline=$request->tagline;
            $slide->title=$request->title;
            $slide->subtitle=$request->subtitle;
            $slide->link=$request->link;
            $slide->status=$request->status;

            if($request->hasFile('image')){
                $fileName=time().'_'.$request->file('image')->getClientOriginalName();
                $path=$request->file('image')->storeAs('uploads/slides',$fileName,'public');
                $slide->image=$path;
            }
            $slide->save();

            return redirect()->back()->with('success', 'Slide created successfully');

        }

        public function editSlide($id){
            $slide=Slide::findOrFail($id);
            return view('admin.editSlide',compact('slide'));
        }

        public function updateSlide(Request $request,$id){

            $request->validate([
                'tagline'=>'required|string|max:255',
                'title'=>'required|string',
                'subtitle'=>'required|string',
                'link'=>'required|url',
                'status'    => 'required|in:0,1',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);
            $slide = Slide::findOrFail($id);
            
            $slide->tagline=$request->tagline;
            $slide->title=$request->title;
            $slide->subtitle=$request->subtitle;
            $slide->link=$request->link;
            $slide->status=$request->status;

            if($request->hasFile('image')){
                //if admin upload new image delete old image
                if ($slide->image && Storage::disk('public')->exists($slide->image)) {
                    Storage::disk('public')->delete($slide->image);
                }
                $fileName=time().'_'.$request->file('image')->getClientOriginalName();
                $path=$request->file('image')->storeAs('uploads/slides',$fileName,'public');
                $slide->image=$path;
            }
            $slide->save();
            return redirect()->back()->with('success', 'Slide updated successfully');
        }

        public function deleteSlide($id){
            $slide = Slide::findOrFail($id);
            $imagePath = public_path('storage/' . $slide->image);
            if(file::exists($imagePath)){
                File::delete($imagePath);
            }
            $slide->delete();
            return redirect()->back()->with('success', value: 'Slide Deleted successfully');

        }
    }        