<?php

namespace App\Http\Controllers\Api;

use App\Bank;
use App\Conversation;
use App\DriverOrder;
use App\Offer;
use App\Order;
use App\Rate;
use App\Setting;
use App\UserDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
class OfferController extends Controller
{
    public function create_offer(Request  $request)
    {
        $rules = [
            'order_id'     => 'required|exists:orders,id',
            'price'        => 'required',
            'latitude'     => 'required',
            'longitude'    => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        $order = Order::find($request->order_id);
        // create new offer
        $offer = Offer::create([
            'driver_id'  => $request->user()->id,
            'order_id'  => $order->id,
            'status'  => '0',
            'price'  => $request->price,
        ]);
        // update driver location
        $request->user()->update([
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
        ]);
        // update driver order to hold order
        $driverOrder = DriverOrder::whereOrderId($order->id)
            ->where('driver_id' , $request->user()->id)
            ->first();
        if ($driverOrder != null)
        {
            $driverOrder->update([
                'status'  => '1'    // hold order
            ]);
        }
        // send  notification to user

        $ar_title = 'العروض';
        $en_title = 'offers';
        $ur_title = 'پیش کرتا ہے';
        $ar_message = 'قام'.' '. $request->user()->name .' ' .' بتقديم عرض سعر لطلبك';
        $en_message =$request->user()->name .' ' .'has made a quote for your order';
        $ur_message = 'ڈرائیوروں میں سے ایک نے آپ کے آرڈر کا حوالہ دیا ہے' .' '. $request->user()->name;
        $devicesTokens =  UserDevice::where('user_id',$order->user->id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        if ($devicesTokens) {
            sendMultiNotification(trans('messages.new_order'), trans('messages.orderNew') ,$devicesTokens);
        }
        saveNotification($order->user->id, $ar_title,$en_title,$ur_title, $ar_message ,$en_message,$ur_message,'2' , $order->id);

        return ApiController::respondWithSuccess(new \App\Http\Resources\Offer($offer));
    }
    public function get_offers(Request $request , $id)
    {
        $order = Order::find($id);
        if ($order)
        {
            if ($order->user_id == $request->user()->id)
            {
                $offers = Offer::whereOrderId($order->id)
                    ->where('status' , '0')
                    ->orderBy('created_at' , 'desc')
                    ->get();
                if ($offers->count() > 0)
                {
                    return ApiController::respondWithSuccess(\App\Http\Resources\Offer::collection($offers));
                }else{
                    $errors = [
                        'key'    => 'Offers',
                        'value'  => trans('messages.NoOffers'),
                    ];
                    return ApiController::respondWithErrorArray(array($errors));
                }
            }else{
                $errors = [
                    'key'   => 'offers',
                    'value' => trans('messages.orderNotBelongToYou')
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
        }else{
            $errors = [
                'key'   => 'offers',
                'value' => trans('messages.order_not_found')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function accept_offer(Request $request , $id)
    {
        $offer = Offer::with('order')
            ->whereHas('order' , function ($q) use ($request){
                $q->where('user_id' , $request->user()->id);
                $q->where('status' , '0');
            })
            ->where('id' , $id)
            ->where('status' , '0')
            ->first();
        if ($offer)
        {
            if ($offer->order->user_id == $request->user()->id)
            {
                // update offer status to accepted
                $offer->update([
                    'status'  => '1'   // accepted
                ]);
                // update Driver Order to Active
                $driverOrder = DriverOrder::whereOrderId($offer->order_id)
                    ->where('driver_id' , $offer->driver_id)
                    ->first();
                $driverOrder->update([
                    'status'  => '2',    // active driver order
                ]);
                // update order status to active and add driver and delivery price
                $offer->order->update([
                    'status'         => '1',  // active  order
                    'driver_id'      => $offer->driver_id,
                    'delivery_price' => $offer->price,
                ]);
                //  create a new conversation with User And Driver
                Conversation::create([
                    'first'    => $request->user()->id,
                    'second'   => $offer->driver_id,
                    'order_id' => $offer->order_id,
                    'status'   => '0',
                ]);
                //  send notification to Driver
                $ar_title = 'العروض';
                $en_title = 'offers';
                $ur_title = 'پیش کرتا ہے';
                $ar_message = 'تم قبول عرض سعرك من  قبل  '.' ' . $offer->order->user->name;
                $en_message = 'Your quotation has been accepted by the '.' ' . $offer->order->user->name;
                $ur_message = 'آپ کا حوالہ گاہک نے قبول کرلیا ہے' .' ' . $offer->order->user->name;
                $devicesTokens =  UserDevice::where('user_id',$offer->driver_id)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                if ($devicesTokens) {
                    sendMultiNotification(trans('messages.offers'), trans('messages.offerAccepted') ,$devicesTokens);
                }
                saveNotification($offer->driver_id,$ar_title,$en_title , $ur_title,$ar_message , $en_message , $ur_message,'3', $offer->order->id , $offer->id);
                $success  = [
                    'key'  => 'accept_offer',
                    'value' => trans('messages.offerAcceptedSuccessfully')
                ];
                return ApiController::respondWithSuccess($success);
            }else{
                $errors = [
                    'key'    => 'accept_offer',
                    'value'  => trans('messages.OfferNotAllowed')
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
        }else{
            $errors = [
                'key'   => 'accept_offer',
                'value' => trans('messages.offerNotFound')
            ];
            return ApiController::respondWithErrorArray(array($errors));
        }
    }
    public function bank_info(Request $request)
    {
        $banks = Bank::all();
        $locale = $request->header('Content-Language');
        $arr = [];
        if ($banks->count() > 0)
        {
            foreach ($banks as $bank) {
                array_push($arr , [
                    'name'           =>$locale == 'ar' ? $bank->ar_name : ($locale == 'en' ? $bank->en_name : $bank->ur_name),
                    'account_number' => $bank->account_number,
                    'IBAN_number'    => $bank->IBAN_number,
                ]);
            }
        }
        return ApiController::respondWithSuccess($arr);
    }
    public function rate(Request $request)
    {
        $rules = [
            'user_id'  => 'required|exists:users,id',
            'rate'     => 'required|numeric'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

        // create new Rate
        Rate::create([
            'from_user_id'  => $request->user()->id,
            'to_user_id'    => $request->user_id,
            'rate'          => $request->rate,
        ]);
        $success = [
            'key'   => 'rate',
            'value' => trans('messages.rated_successfully')
        ];
        return ApiController::respondWithSuccess($success);
    }
}
