<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutController extends Controller
{
    //
    public function getAbout(Request $request){
        return view('about');
    }
}
