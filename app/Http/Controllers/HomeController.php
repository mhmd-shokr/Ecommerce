<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    
    public function index()
    {
        $slides=Slide::where('status',1)->take(3)->get();
        return view('index',compact('slides'));
    }
}
;