<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{

    public function index(){
        $categories=Category::with('products')->withCount('products')->get();
        return response()->json([
            "data"=> CategoryResource::collection($categories),
            'message'=>'Success'
        ],200);
    }

    public function store(StoreCategoryRequest $request){
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
        return response()->json([
            'message'=>'category created successfully',
        ],201);
    }

    public function update(UpdateCategoryRequest $request,Category $category){
        $imageName = $category->image;
        
        if($request->hasFile('image')){
            // path of file will save in
            $destinationPath = public_path('uploads/categories');
    
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
    
            // delete old image if exists
            if ($imageName && file_exists($destinationPath . '/' . $imageName)) {
                unlink($destinationPath . '/' . $imageName);
            }
    
            // save fileName without extension 
            $originalName = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
            // save extension 
            $extension = $request->image->getClientOriginalExtension();
            // combine fileName + timestamp + extension
            $imageName = Str::slug($originalName) . '-' . time() . '.' . $extension;
    
            $image = Image::make($request->file('image')->getRealPath());
            $image->fit(300, 300, function ($constraint) {
                $constraint->upsize();
            });
            $image->save($destinationPath . '/' . $imageName);
        }
    
        $category->update([
            'name'=>$request->name ?? $category->name,
            'slug'=>Str::slug($request->slug ?? $category->name),
            'image' => $imageName,
        ]);
    return response()->json([
        'message'=>'category updated successfully',
        "data"=>$category,
    ],200);
}
public function destroy($id){
    $category = Category::findOrFail($id);
    $destinationPath = public_path('uploads/categories/');
    $imagePath = $destinationPath . $category->image;

    if (!empty($category->image) && file_exists($imagePath) && is_file($imagePath)) {
        unlink($imagePath);
    }

    $category->delete();

    return response()->json([
        'message' => 'Category deleted successfully',
    ], 200);
}

}
