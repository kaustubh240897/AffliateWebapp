<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RecentClick;

class RecentClickController extends Controller
{
    //
    public function getlist(Request $request){
        $getrecentclicks = RecentClick::where('user_id', Auth::id())->paginate(10);
        // if($request->ajax()){
        //     $view  = view('data', compact('getrecentclicks'))->render();
        //     return response()->json(['html'=>$view]);
        // }
        return view('recentclick', compact('getrecentclicks'));
    }
}
