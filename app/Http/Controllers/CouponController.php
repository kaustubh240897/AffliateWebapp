<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Blog;
use App\Models\FlexImage;
use App\Models\TopCashbackStore;
use App\Models\TopCategory;
use App\Models\TopSellingProduct;
use App\Models\IndividualProduct;
use App\Models\RecentClick;
use App\Models\Subscribe;
use App\Models\UntrackedSearchQuery;
use App\Imports\CouponsImport;
use Excel;
use \GuzzleHttp\Client;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\ConnectException;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $id = Auth::id();
        // dd($id);
        

        $date = Carbon::now()->format("Y-m-d");
        $allFlex = FlexImage::all();
        $allStores = TopCashbackStore::all();
        $allCategory = TopCategory::all();
        $allProductsList = TopSellingProduct::all();
        $allIndividualProducts = IndividualProduct::all();
        $allCoupons = Coupon::all();
        $couponsByBrand = Coupon::all()->groupBy('merchant');
        //dd($couponsByBrand);
        $couponsByCategory = Coupon::all()->groupBy('categories');
    
        
        return view('home', compact('allCoupons', 'couponsByCategory','couponsByBrand', 'allFlex', 'allStores', 'allCategory', 'allIndividualProducts', 'allProductsList'));
    }


    public function storeSubscriber(Request $request){

        $request->validate([
            'email' => 'required|unique:subscribes,email,'.$request->subsemail,
        ]);
        $subscriber = new Subscribe;
        $subscriber->email = $request->email;
        $subscriber->save();
        return redirect(route('index'))->with('successMsg','Thankyou, You have Successfully subscribed.');

    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }


    public function importcoupons()
    {
        return view('import-form');
    }

    public function import(Request $request)
    {
        try {
            if (Coupon::all()->count() == 0) {
                Excel::import(new CouponsImport, $request->file);
                return redirect(route('importcoupons'))->with('successMsg', 'your csv file Successfully added.');
            } else {
                $coupons = Coupon::all();
                foreach ($coupons as $coupon) {
                    $coupon->delete();
                }
                Excel::import(new CouponsImport, $request->file);
                return redirect(route('importcoupons'))->with('warningMsg', 'your csv file successfully updated.');
            }
        } catch (\Exception $error) {
            return $error->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $coupon =  Coupon::where('id', $id)->first();
        if ($coupon != null) {
            return view('coupondetails', compact('coupon'));
        } else {
            //$coupon = null;
            return view('coupondetails', compact('coupon'));
        }
    }


    public function showBrandCoupons($brand)
    {
        $date = Carbon::now()->format("Y-m-d");
        $couponslist =  Coupon::where('merchant', 'like', "%{$brand}%")->get();
        if ($couponslist != null) {
            return view('brandcoupondetails', compact('couponslist'));
        } else {
            $couponslist = "Not exist";
            return view('brandcoupondetails', compact('couponslist'));
        }
    }

    public function showCategoryCoupons($category)
    {
        $date = Carbon::now()->format("Y-m-d");
        $couponslist =  Coupon::where('categories', 'like', "%{$category}")->get();
        if ($couponslist != null) {
            return view('categorycoupondetails', compact('couponslist'));
        } else {
            $couponslist = "Not exist";
            return view('categorycoupondetails', compact('couponslist'));
        }
    }




    public function search(Request $request)
    {
        try {
            $client = new client();

            $key = trim($request->get('q'));
            //dd($key);
            $base_url1 = env('BASE_API_URL', 'http://localhost:8000/');
            $request  = $client->get($base_url1 . $key . "/");
            $f = json_decode($request->getBody(), true);
            //dd($f);
            $s = "";
            if ($f) {
                foreach ($f['data'] as $element) {
                    $category = $element['category'];
                    //echo($category." , ");
                    $s .= $category;
                    //Now here you can send it to the modele and create your db row.
                }
            } else {
                $s = null;
            }
            //dd($s);
            $res = str_ireplace(array(
                '\'', '"',
                ',', ';', '<', '>', "|"
            ), '', $s);
            $pieces = explode("  ", $res);
            $aa = [];
            foreach ($pieces as $a) {
                $aa[] = $a;
            }




            //dd($aa);
            $date = Carbon::now()->format("Y-m-d");

            $allCoupons = Coupon::all();
            $couponsByBrand = Coupon::all()->where('endDate', '>=', $date)->unique('campaignName');
            $couponsByCategory = Coupon::all()->where('endDate', '>=', $date)->unique('categories');
            $date = Carbon::now()->toDateString();

            $search_coupons = Coupon::query()
                ->where('merchant', 'like', "%{$key}%")
                ->orwhere('categories', 'like', "%{$key}%")
                ->orWhere('description', 'like', "%{$key}%")
                ->orwhereIn('categories', $aa)
                ->orderBy('startDate', 'asc')
                ->get();
            if($search_coupons->isEmpty()){
                $search_query = new UntrackedSearchQuery;
                $search_query->query = $key;
                $search_query->save();

            }

            $search_merchants = Coupon::query()
                ->where('merchant', 'like', "%{$key}%")
                ->orwhere('categories', 'like', "%{$key}%")
                ->orWhere('description', 'like', "%{$key}%")
                ->orwhereIn('categories', $aa)
                ->get()->unique('merchant');

            $q = [];
            foreach ($search_merchants as $search_merchant) {
                $base_url = env('BASE_API_BRAND_URL', 'http://localhost:8000/');
                $url = $base_url . $search_merchant->merchant . '/';
                $request  = $client->get($url);
                $f = json_decode($request->getBody(), true);


                if ($f['data'] != null) {
                    foreach ($f['data'] as $element) {
                        //echo($element[2]);
                        $category = $element['url'];
                        //echo($category." , ");
                        $q[] = $category;
                    }
                } else {
                    $q[] = "/";
                }
            }


            //dd($q);



            return view('search', [
                'client' => $client,
                'key' => $key,
                'brand_urls' => $q,
                'search_coupons' => $search_coupons,
                'search_merchants' => $search_merchants,
                'couponsByBrand' => $couponsByBrand,
                'allCoupons'   => $allCoupons,
                'couponsByCategory' => $couponsByCategory,
                'today_date' => $date,
            ]);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            echo 'Message: The search server is down, Sorry for your trouble. ';
            exit;
        }
    }

    public function storerecentclick(Request $request)
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        $geoipinfo_country = geoip()->getLocation($ipaddress)->country;
        $geoipinfo_city = geoip()->getLocation($ipaddress)->city;
        $geoipinfo_state = geoip()->getLocation($ipaddress)->state_name;
        $geoipinfo_currency = geoip()->getLocation($ipaddress)->currency;
        $combine = $geoipinfo_country." ".$geoipinfo_city." ".$geoipinfo_state." ".$geoipinfo_currency;

        $recent_clicks = new RecentClick;
        $recent_clicks->user_id = Auth::id();
        $recent_clicks->coupon_id = $request->coupon_id;
        $recent_clicks->ip_address = $ipaddress;
        $recent_clicks->location = $combine;
        $recent_clicks->save();
        return redirect($request->link);
    }

    public function getapidata()
    {
        $client = new client();
        // $body['headers'] = array('');
        $base_url = env('BASE_API_URL', 'http://localhost:8000/');
        $request  = $client->get($base_url . 'shirt');

        //dd(json_decode($request->getBody()->getContents()));
        $f = json_decode($request->getBody(), true);
        //$data = $f->data;
        foreach ($f['data'] as $element) {
            $category = $element['category'];
            echo ($category . " , ");
            //Now here you can send it to the modele and create your db row.
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
