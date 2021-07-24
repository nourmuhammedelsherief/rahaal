<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Validator;
use App\User;
use App;
use Auth;

class ProfileController extends Controller
{
    //

    public function about_us()
    {



        $about = App\AboutUs::first();
        $all=[
            'title'=>$about->title,
            'content'=>$about->content,
        ];


        return ApiController::respondWithSuccess($all);
    }
    public function get_user_data($id)
    {
        $user = User::find($id);
        if ($user)
        {
            return ApiController::respondWithSuccess(new App\Http\Resources\User($user));
        }else{
            $errors = [
                'key'   => 'get_user_data',
                'value' => 'not found ya lemmpy'
            ];
            return ApiController::respondWithErrorClient(array($errors));
        }
    }
    public function terms_and_conditions()
    {



        $terms = App\TermsCondition::first();
        $all=[
            'title'=>$terms->title,
            'content'=>$terms->content,
        ];


        return ApiController::respondWithSuccess($all);
    }

    public function sawaq_offers_price(Request $request)
    {

        $offers = App\SawaqOfferPrice::where('user_id',$request->user()->id)->get();
        $count= $offers->count();
        $currentPage =$request->page;
        $perPage=10;
        $currentPageItems1 = $offers->slice(($currentPage - 1) * $perPage, $perPage);
        $all=[];
        foreach ($currentPageItems1 as $offer){
            array_push($all,[
                'id'=>$offer->id,
                'price'=>$offer->price,
                'order_id'=>$offer->order_id,
                'sawaq_user_id'=>$offer->sawaq_user_id,
                'username'=>User::find($offer->sawaq_user_id)->name,
                'created_at'=>$offer->created_at->format('Y-m-d') ,
            ]);
        }

        $data=[];
        array_push($data , ['offers'=> $all , 'count'=> $count]);
        return ApiController::respondWithSuccess($data);
    }
    public function my_order(Request $request)
    {

        $offers = App\Order::where('user_id',$request->user()->id)->get();
        $count= $offers->count();
        $currentPage =$request->page;
        $perPage=10;
        $currentPageItems1 = $offers->slice(($currentPage - 1) * $perPage, $perPage);
        $all=[];
        foreach ($currentPageItems1 as $offer){
            array_push($all,[

                'user_id'=>$request->user()->id,
                'order_type'=>intval($offer->order_type),
                'from_city_id'=>intval($offer->from_city_id),
                'from_city'=>App\City::find($offer->from_city_id)->name,
                'from_region_id'=>intval($offer->from_region_id),
                'from_region'=>App\City::find($offer->from_region_id)->name,
                'deliver_time'=>intval($offer->deliver_time),
                'from_time'=>$offer->from_time,
                'to_time'=>$offer->to_time,
                'status'=>$offer->status,
                'price'=>$offer->price,
                'sawaq_user_id'=>$offer->sawaq_user_id,
                'created_at'=>$offer->created_at->format('Y-m-d'),
                'phone_number'=>User::find($request->user()->id)->phone_number,
                'username'=>User::find($request->user()->id)->name,
                'to_region_id'=>intval($offer->to_region_id),
                'to_region'=> $offer->order_type == 2 ?   App\City::find($offer->to_region_id)->name : null,
                'address'=>$offer->address,
                'start_date'=>$offer->start_date,
                'end_date'=>$offer->end_date,
                'id'=>intval($offer->id),
                'to_school'=>intval($offer->to_school),
                'school'=>$offer->order_type == 1 ? App\School::find($offer->to_school)->name : null,


            ]);

        }

        $data=[];
        array_push($data , ['orders'=> $all , 'count'=> $count]);
        return ApiController::respondWithSuccess($data);
    }
    public function order_details($id , Request $request)
    {

        $offers = App\Order::find($id);
        if ($offers == null){
            $errors = ['key'=>'message',
                'value'=> trans('messages.not_found')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }


        $data=[
            'id'=>intval($offers->id),
            'order_type'=>intval($offers->order_type),
            'commission_status'=>App\SawaqOfferPrice::where('order_id',$offers->id)->where('sawaq_user_id',$offers->sawaq_user_id)->first() == null ? null : App\SawaqOfferPrice::where('order_id',$offers->id)->where('sawaq_user_id',$offers->sawaq_user_id)->first()->commission_status,
            'from_city_id'=>intval($offers->from_city_id),
            'from_city'=>App\City::find($offers->from_city_id)->name,
            'from_region_id'=>intval($offers->from_region_id),
            'from_region'=>App\City::find($offers->from_region_id)->name,
            'deliver_time'=>intval($offers->deliver_time),
            'from_time'=>$offers->from_time,
            'to_time'=>$offers->to_time,
            'status'=>$offers->status,
            'price'=>$offers->price,
            'sawaq_user_id'=>$offers->sawaq_user_id,
            'user_id'=>$offers->user_id,
            'created_at'=>$offers->created_at->format('Y-m-d'),
            'phone_number'=>User::find($offers->user_id)->phone_number,
            'username'=>User::find($offers->user_id)->name,
            'to_region_id'=>intval($offers->to_region_id),
            'to_region'=> $offers->order_type == 2 ?   App\City::find($offers->to_region_id)->name : null,
            'address'=>$offers->address,
            'start_date'=>$offers->start_date,
            'end_date'=>$offers->end_date,
            'to_school'=>intval($offers->to_school),
            'school'=>$offers->order_type == 1 ? App\School::find($offers->to_school)->name : null,


        ];

        return ApiController::respondWithSuccess($data);
    }
    public function order_offers($id , Request $request)
    {

        $offers = App\Order::find($id);
        if ($offers == null){
            $errors = ['key'=>'message',
                'value'=> trans('messages.not_found')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }

        $offers = App\SawaqOfferPrice::where('order_id',$id)->get();
        $count= $offers->count();
        $currentPage =$request->page;
        $perPage=10;
        $currentPageItems1 = $offers->slice(($currentPage - 1) * $perPage, $perPage);
        $all=[];
        foreach ($currentPageItems1 as $offer){
            array_push($all,[
                'id'=>$offer->id,
                'price'=>$offer->price,
                'order_id'=>$offer->order_id,
                'sawaq_user_id'=>$offer->sawaq_user_id,
                'username'=>User::find($offer->sawaq_user_id)->name,
                'created_at'=>$offer->created_at->format('Y-m-d') ,
            ]);
        }

        $data=[];
        array_push($data , ['offers'=> $all , 'count'=> $count]);
        return ApiController::respondWithSuccess($data);
    }
    public function rate_driver_user($id,Request $request)
    {

        $rules = [
            'rate'=> 'required|in:1,2,3,4,5',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $order= App\User::where('id',$id)->where('type',1)->first();
        if ($order == null){
            $errors = ['key'=>'message',
                'value'=> trans('messages.not_found')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
        $user= User::find($request->user()->id);

        $orders=App\Rate::updateOrCreate(
            ['from_user_id' => $request->user()->id, 'to_user_id' => $order->id],
            [ 'rate' => $request->rate]
        );
//        $devicesTokens = App\UserDevice::where('user_id', $order->professional_user_id)
//            ->get()
//            ->pluck('device_token')
//            ->toArray();
//
//        if ($devicesTokens) {
//            sendMultiNotification($order->title, trans('messages.The_customer') . " : ".$user->name." ".trans('messages.evaluated_you'). " ". $request->rate." ".trans('messages.on_your_service').$order->title ,$devicesTokens);
//        }
//        saveNotification($order->professional_user_id, $order->title , '1', trans('messages.The_customer') . " : ".$user->name." ".trans('messages.evaluated_you'). " ". $request->rate." ".trans('messages.on_your_service').$order->title);

        return $orders
            ? ApiController::respondWithSuccess([])
            : ApiController::respondWithServerErrorArray();

    }
    /**
     * @get the werash range
     * @get_range
     */
    public function get_range(){
        $range = App\Range::orderBy('created_at' , 'desc')->first();
        return ApiController::respondWithSuccess([
            'range' => intval($range->range)
        ]);
    }

}
