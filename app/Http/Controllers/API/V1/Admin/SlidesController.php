<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SlidesController extends Controller
{
    public function index(){
        $slides=Slide::orderBy('id','desc')->paginate(12);
        return response()->json([
            "data"=>$slides,
            "message"=>'success',
        ],200);
    }


    public function store(Request $request){
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

        return response()->json([
            "data"=>$slide,
            "message"=>'Slide created successfully',
        ],201);

    }

    public function update(Request $request,Slide $slide){

        $request->validate([
            'tagline'=>'sometimes|string|max:255',
            'title'=>'sometimes|string',
            'subtitle'=>'sometimes|string',
            'link'=>'sometimes|url',
            'status'    => 'sometimes|in:0,1',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        
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
            $path=$request->file('image')->storeAs('app/public/slides',$fileName,'public');
            $slide->image=$path;
        }
        $slide->save();
        return response()->json([
            "data"=>$slide,
            "message"=>'Slide updated successfully',
        ],200);
    }

    public function destroy($id){
        $slide = Slide::findOrFail($id);
        $imagePath = public_path('storage/' . $slide->image);
        if(file::exists($imagePath)){
            File::delete($imagePath);
        }
        $slide->delete();
        return response()->json([
            "message" =>'Slide Deleted successfully'
        ],200);

    }
}
