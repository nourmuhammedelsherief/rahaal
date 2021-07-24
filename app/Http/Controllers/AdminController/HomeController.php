<?php

namespace App\Http\Controllers\AdminController;

use App\City;
use App\Food;
use App\FoodRequest;
use App\Http\Controllers\Controller;
use App\Order;
use App\User;
use App\UserDevice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $admins = DB::table('admins')->count();
        $users =  DB::table('users')->where('type' , '1')->count();
        $drivers =  DB::table('users')->where('type' , '2')->count();
        $countries = DB::table('countries')->count();
        $vehicles = DB::table('trucks')->count();
        $trucks_types = DB::table('truck_types')->count();
        $vehicle_brands = DB::table('vehicle_brands')->count();
        $banks = DB::table('banks')->count();
        $new = DB::table('orders')->where('status' , '0')->count();
        $active = DB::table('orders')->where('status' , '1')->count();
        $finished = DB::table('orders')->where('status' , '2')->count();
        $canceled = DB::table('orders')->where('status' , '3')->count();
        return view('admin.home' , compact('admins' , 'new','active','finished','canceled','banks', 'vehicle_brands' ,'vehicles','users' , 'drivers' , 'countries' , 'trucks_types'));
    }
    public function get_regions($id)
    {
        $regions = City::where('parent_id',$id)->select('id','name')->get();
        $data['regions']= $regions;
        return json_encode($data);
    }
    public function public_notifications()
    {
        return view('admin.public_notifications');
    }
    public function store_public_notifications(Request $request)
    {
        $this->validate($request , [
            "type"       => "required|in:1,2",
            "ar_title"   => "required",
            "en_title"   => "required",
            "ur_title"   => "required",
            "ar_message" => "required",
            "en_message" => "required",
            "ur_message" => "required",
        ]);
        // Create New Notification

        $users = User::whereType($request->type)->where('active' , '1')->get();
        foreach ($users as $user)
        {
            $ar_title = $request->ar_title;
            $en_title = $request->en_title;
            $ur_title = $request->ur_title;
            $ar_message = $request->ar_message;
            $en_message = $request->en_message;
            $ur_message = $request->ur_message;
            $devicesTokens =  UserDevice::where('user_id',$user->id)
                ->get()
                ->pluck('device_token')
                ->toArray();
            if ($devicesTokens) {
                sendMultiNotification($ar_title, $ar_message ,$devicesTokens);
            }
            saveNotification($user->id, $ar_title,$en_title,$ur_title, $ar_message ,$en_message,$ur_message,'0' , null , null);

        }
        flash('تم ارسال الاشعار بنجاح')->success();
        return redirect()->route('public_notifications');

    }
    public function user_notifications()
    {
        return view('admin.user_notification');
    }
    public function store_user_notifications(Request $request)
    {
        $this->validate($request, [
            'user_id*'   => 'required',
            "ar_title"   => "required",
            "en_title"   => "required",
            "ur_title"   => "required",
            "ar_message" => "required",
            "en_message" => "required",
            "ur_message" => "required",
        ]);
        foreach ($request->user_id as $one) {
            $user = User::find($one);
            $ar_title = $request->ar_title;
            $en_title = $request->en_title;
            $ur_title = $request->ur_title;
            $ar_message = $request->ar_message;
            $en_message = $request->en_message;
            $ur_message = $request->ur_message;
            $devicesTokens =  UserDevice::where('user_id',$user->id)
                ->get()
                ->pluck('device_token')
                ->toArray();
            if ($devicesTokens) {
                sendMultiNotification($ar_title, $ar_message ,$devicesTokens);
            }
            saveNotification($user->id, $ar_title,$en_title,$ur_title, $ar_message ,$en_message,$ur_message,'0' , null , null);
        }
        flash('تم ارسال الاشعار للمستخدمين بنجاح')->success();
        return redirect()->route('user_notifications');
    }
    public function orders($status)
    {
        $orders = Order::whereStatus($status)->get();
        if($status == '0')
        {
            return view('admin.orders.new' , compact('orders'));
        }elseif ($status == '1')
        {
            return view('admin.orders.active' , compact('orders'));
        }elseif ($status == '2')
        {
            return view('admin.orders.finished' , compact('orders'));
        }elseif ($status == '3')
        {
            return view('admin.orders.canceled' , compact('orders'));
        }
    }
}
