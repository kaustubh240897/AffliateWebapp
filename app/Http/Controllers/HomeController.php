<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $allCoupons = Coupon::all();
        $couponsByBrand = Coupon::all()->unique('campaignName');
        $couponsByCategory = Coupon::all()->unique('categories');
        return view('home', compact('allCoupons', 'couponsByBrand', 'couponsByCategory'));
    }

    public function userprofile()
    {
        $user = User::where('id', Auth::id())->first();
        return view('profile', compact('user'));
    }

    public function edit_userprofile(Request $request)
    {
        $user1 = User::find(Auth::id());
        $user1->name = $request->name;
        $user1->save();
        return redirect(route('profile'))->with('successMsg', 'your info Successfully updated');
    }


    public function showCouponAuth($id)
    {
        $coupon =  Coupon::where('id', $id)->first();
        if ($coupon != null) {
            return view('coupondetails', compact('coupon'));
        } else {
            //$coupon = null;
            return view('coupondetails', compact('coupon'));
        }
    }
}
