<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class BrandsController extends Controller
{
    public function index(){
        $brands=Brand::orderBy('id','desc')->paginate(10);
        return response()->json([
            'data'=> BrandResource::collection ($brands),
            "message"=>'success',
        ]);
    }



    public function store(Request $request){
        $request->validate([
            'name'=>'required|string',
            'slug'=>'required|unique:brands,slug',
            'image'=>'nullable|mimes:png,jpg,jpeg|max:2048',
        ]);
        $imageName = null;
        if($request->hasFile('image')){
            //path of file will save in
            $destinationPath = storage_path('app/public/brands');
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
        return  response()->json([
            "data"=>$brand,
            "message"=>'success',
        ],201);
    }
    
        public function update(Request $request,Brand $brand)
        {
            $request->validate([
                'name'=>'sometimes|string',
                'slug'=>'sometimes|unique:brands,slug,'.$brand->id,
                'image'=>'sometimes|mimes:png,jpg,jpeg|max:2048',
            ]);
                $imageName = $brand->image;
                if($request->hasFile('image')){
                    //path of file will save in
                    $destinationPath = storage_path('app/public/brands');
        
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
                return response()->json([
                    "data"=>$brand,
                    "message"=>'success updated',
                ],200);    
        }
            
        public function destroy(Brand $brand){
            $destinationPath = storage_path('app/public/brands/');
            $imagePath=$destinationPath.$brand->image;
            if (!empty($brand->image) && file_exists($imagePath) && is_file($imagePath)) {
                unlink($imagePath);
            }
            $brand->delete();
            return response()->json([
                "message"=>'Brand deleted successfully',
            ]);
        }
}
