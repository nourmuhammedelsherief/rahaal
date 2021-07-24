<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\DriverOrder;
use App\Electronic_wallet;
use App\History;
use App\Offer;
use App\Order;
use App\Setting;
use App\Truck;
use App\User;
use App\UserDevice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use function Matrix\trace;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $user = $request->user();
//        $user_last_order = Order::whereUserId($user->id)->orderBy('id' , 'desc')->first();
//        if ($user_last_order != null)
//        {
//            if ($user_last_order->status == '0' || $user_last_order->status == '1')
//            {
//                $errors = [
//                    'key'   => 'create_order',
//                    'value' => trans('messages.uCanNotCreateOrder')
//                ];
//                return ApiController::respondWithErrorArray(array($errors));
//            }
//        }
//        $order_limit = Setting::find(1)->order_limit;
//        $wallet_check = Electronic_wallet::whereUserId($request->user()->id)->first();
//        if ($wallet_check == null || $wallet_check->amount < $order_limit)
//        {
//            $order_errors = [
//                'key'   => 'create_order',
//                'value' => trans('messages.WalletNoBalance'),
//            ];
//            return ApiController::respondWithErrorAuthArray(array($order_errors));
//        }
        $rules = [
            'truck_type_id'  => 'required|exists:truck_types,id',
            'latitude_from'  => 'required',
            'longitude_from' => 'required',
            'latitude_to'    => 'required',
            'longitude_to'   => 'required',
            'type'           => 'required',    // text
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));
        // check  if the drivers trucks  has the same truck type  that  user search
        $check = Truck::where('truck_type_id', $request->truck_type_id)->get();
        if ($check->count() <= 0) {
            $errors = [
                'key' => 'create_order',
                'value' => trans('messages.noTruckYouSearch')
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
        $user = User::find($request->user()->id);
        // create a new order
        $order = Order::create([
            'user_id' => $request->user()->id,
            'truck_type_id' => $request->truck_type_id,
            'status' => '0',                   // new order
            'latitude_from' => $request->latitude_from,
            'longitude_from' => $request->longitude_from,
            'latitude_to' => $request->latitude_to,
            'longitude_to' => $request->longitude_to,
            'type' => $request->type,
        ]);
        // send Notification To Drivers
        $range = Setting::find(1)->search_range;
        $lat = $order->latitude_from;
        $lon = $order->longitude_from;
        $drivers = User::with('driver_orders', 'trucks')
            ->whereHas('driver_orders', function ($q) {
                $q->where('status', '!=', '2'); // driver do not have an active orders
            })->whereHas('trucks', function ($q) use ($request) {
                $q->where('truck_type_id', $request->truck_type_id); // driver that have the same truck that user search
                $q->where('status' , '1');
            })
            ->selectRaw('*, ( 6367 * acos( cos( radians( ? ) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians( ? ) ) + sin( radians( ? ) ) * sin( radians( latitude) ) ) ) AS distance', [$lat, $lon, $lat])
            ->having('distance', '<=', $range)
            ->where('type', 2)
            ->where('active', 1)
            ->where('country_id', $request->user()->country_id)
            ->where('availability', '1')
            ->orderBy('distance')
            ->get();
        if ($drivers->count() > 0) {
            foreach ($drivers as $driver) {
                // save order to drivers
                DriverOrder::create([
                    'driver_id' => $driver->id,
                    'order_id' => $order->id,
                    'status' => '0',
                ]);
                $ar_title = 'طلب جديد';
                $en_title = 'New Order';
                $ur_title = 'نیا حکم';
                $ar_message = 'تفحص  الطلبات الجديدة';
                $en_message = 'Check New Orders';
                $ur_message = 'نئے احکامات چیک کریں';
                $devicesTokens = UserDevice::where('user_id', $driver->id)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                if ($devicesTokens) {
                    sendMultiNotification(trans('messages.new_order'), trans('messages.orderNew'), $devicesTokens);
                }
                saveNotification($driver->id, $ar_title, $en_title, $ur_title, $ar_message, $en_message, $ur_message, '1', $order->id);
            }
        }
//                return $this->prepare_order($request , $order->id);
        return $order
            ? ApiController::respondWithSuccess(new \App\Http\Resources\Order($order))
            : ApiController::respondWithServerErrorArray();
    }

    public function finish_order(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        if ($order != null) {
            if ($order->user_id == $request->user()->id && $order->status == '1') {
                // user pay Order

                $rules = [
                    'payment_method' => 'required|in:0,1',  // 0  -> bank transfer  , cash  , 1 -> electronic wallet
                ];
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails())
                    return ApiController::respondWithErrorArray(validateRules($validator->errors(), $rules));

                if ($request->payment_method == '1') {
                    // wallet payment
                    $order_limit = Setting::find(1)->order_limit;
                    $wallet_check = Electronic_wallet::whereUserId($request->user()->id)->first();
                    if ($wallet_check == null || $wallet_check->amount < $order_limit || $wallet_check->amount < $order->delivery_price) {
                        $order_errors = [
                            'key' => 'finish_order',
                            'value' => trans('messages.WalletNoBalance'),
                        ];
                        return ApiController::respondWithErrorAuthArray(array($order_errors));
                    }
                    // calculate commission form driver
                    $driver_commission = Setting::find(1)->driver_commission;
                    $commission = ($order->delivery_price * $driver_commission) / 100;
                    $driver_value = $order->delivery_price - $commission;

                    // add operations to Driver and User wallet
                    $user_wallet = Electronic_wallet::whereUserId($request->user()->id)->first();
                    $user_amount = $user_wallet->amount - $order->delivery_price;
                    $user_wallet->update([
                        'amount' => $user_amount,
                    ]);
                    $Driver_wallet = Electronic_wallet::whereUserId($order->driver->id)->first();
                    if ($Driver_wallet) {
                        $driver_amount = $Driver_wallet->amount + $driver_value;
                        $Driver_wallet->update([
                            'amount' => $driver_amount,
                        ]);
                    } else {
                        // create new wallet to driver
                        Electronic_wallet::create([
                            'user_id' => $order->driver->id,
                            'amount' => $driver_value,
                        ]);
                    }
                    History::create([
                        'user_id' => $order->user->id,
                        'ar_title' => 'نم خصم سعر التوصيل من محفظتك الألكترونية',
                        'en_title' => 'The delivery price has been deducted from your e-wallet',
                        'ur_title' => 'آپ کے ای بٹوے سے ترسیل کی قیمت کم کردی گئی ہے',
                        'price' => $order->delivery_price,
                    ]);
                    History::create([
                        'user_id' => $order->driver->id,
                        'ar_title' => 'تم أضافة سعر توصيل الطلب الي محفظتك الألكترونية',
                        'en_title' => 'The delivery price of the order has been added to your electronic wallet',
                        'ur_title' => 'آرڈر کی ترسیل کی قیمت آپ کے الیکٹرانک پرس میں شامل کردی گئی ہے',
                        'price' => $driver_value,
                    ]);
                    History::create([
                        'user_id' => $order->driver->id,
                        'ar_title' => 'تم خصم عمولة التطبيق من محفظتك الألكترونية',
                        'en_title' => 'The application commission has been deducted from your electronic wallet',
                        'ur_title' => 'آپ کے الیکٹرانک پرس سے ایپلیکیشن کمیشن کاٹ لیا گیا ہے',
                        'price' => $commission,
                    ]);
                    $order->update([
                        'status'            => '2',  // order completed
                        'commission_status' => '1',  // paid
                        'commission_value'  => $commission,
                    ]);
                    // update driver order
                    $driverOrder = DriverOrder::whereOrderId($order->id)
                        ->where('driver_id', $order->driver_id)
                        ->first();
                    $driverOrder->update([
                        'status' => '3' // completed
                    ]);
                    // delete order offers
                    $offers = Offer::whereOrderId($order->id)->get();
                    if ($offers->count() > 0) {
                        foreach ($offers as $offer) {
                            $offer->delete();
                        }
                    }
                    // delete order conversation
                    $conversation = Conversation::whereOrderId($order->id)->first();
                    if ($conversation) {
                        $conversation->delete();
                    }
                    // send notification to driver
                    $ar_title = 'الطلبات';
                    $en_title = 'Orders';
                    $ur_title = 'احکامات';
                    $ar_message = 'قام '.' '.$order->user->name.' '.' بأنهاء الطلب بنجاح';
                    $en_message = 'The '.' '.$order->user->name.' '.' has completed the order successfully';
                    $ur_message = ''.' '.$order->user->name.' '.' نے آرڈر کامیابی کے ساتھ مکمل کرلیا ہے';
                    $devicesTokens = UserDevice::where('user_id', $order->driver->id)
                        ->get()
                        ->pluck('device_token')
                        ->toArray();
                    if ($devicesTokens) {
                        sendMultiNotification($ar_title, $ar_message, $devicesTokens);
                    }
                    saveNotification($order->driver->id, $ar_title, $en_title, $ur_title, $ar_message, $en_message, $ur_message, '1', $order->id);
                    $success = [
                        'data' => 'success',
                        'value' => trans('messages.order_completed_successfully')
                    ];
                    return $order
                        ? ApiController::respondWithSuccess($success)
                        : ApiController::respondWithServerErrorArray();
                } elseif ($request->payment_method == '0') {
                    // cash or bank  transfer payment
                    // calculate commission form driver
                    $driver_commission = Setting::find(1)->driver_commission;
                    $commission = ($order->delivery_price * $driver_commission) / 100;
                    // add operations to Driver and User wallet

                    $Driver_wallet = Electronic_wallet::whereUserId($order->driver->id)->first();
                    if ($Driver_wallet) {
                        $amount = $Driver_wallet->amount - $commission;
                        $Driver_wallet->update([
                            'amount' => $amount,
                        ]);
                    } else {
                        // create new wallet to driver
                        Electronic_wallet::create([
                            'user_id' => $order->driver->id,
                            'amount' => -$commission,
                        ]);
                    }
                    History::create([
                        'user_id' => $order->driver->id,
                        'ar_title' => 'تم خصم عمولة التطبيق من محفظتك الألكترونية',
                        'en_title' => 'The application commission has been deducted from your electronic wallet',
                        'ur_title' => 'آپ کے الیکٹرانک پرس سے ایپلیکیشن کمیشن کاٹ لیا گیا ہے',
                        'price' => $commission,
                    ]);
                    $order->update([
                        'status' => '2',     // order completed
                        'commission_value'  => $commission,
                    ]);
                    // update driver order
                    $driverOrder = DriverOrder::whereOrderId($order->id)
                        ->where('driver_id', $order->driver_id)
                        ->first();
                    $driverOrder->update([
                        'status' => '3' // completed
                    ]);
                    // delete order offers
                    $offers = Offer::whereOrderId($order->id)->get();
                    if ($offers->count() > 0) {
                        foreach ($offers as $offer) {
                            $offer->delete();
                        }
                    }
                    // delete order conversation
                    $conversation = Conversation::whereOrderId($order->id)->first();
                    if ($conversation) {
                        $conversation->delete();
                    }
                    // send notification to driver
                    $ar_title = 'الطلبات';
                    $en_title = 'Orders';
                    $ur_title = 'احکامات';
                    $ar_message = 'قام '.' '.$order->user->name.' '.' بأنهاء الطلب بنجاح';
                    $en_message = 'The '.' '.$order->user->name.' '.' has completed the order successfully';
                    $ur_message = ''.' '.$order->user->name.' '.' نے آرڈر کامیابی کے ساتھ مکمل کرلیا ہے';
                    $devicesTokens = UserDevice::where('user_id', $order->driver->id)
                        ->get()
                        ->pluck('device_token')
                        ->toArray();
                    if ($devicesTokens) {
                        sendMultiNotification($ar_title, $ar_message, $devicesTokens);
                    }
                    saveNotification($order->driver->id, $ar_title, $en_title, $ur_title, $ar_message, $en_message, $ur_message, '1', $order->id);
                    $success = [
                        'data' => 'success',
                        'value' => trans('messages.order_completed_successfully')
                    ];
                    return $order
                        ? ApiController::respondWithSuccess($success)
                        : ApiController::respondWithServerErrorArray();
                }
            } else {
                $errors = [
                    'key' => 'finish_order',
                    'value' => trans('messages.orderNotBelongToYou')
                ];
                return ApiController::respondWithErrorClient(array($errors));
            }
        } else {
            $errors = [
                'key' => 'finish_order',
                'value' => trans('messages.order_not_found')
            ];
            return ApiController::respondWithErrorClient(array($errors));
        }
    }

    /**
     *  new orders for drivers
     * @user_orders
     */
    public function user_orders(Request $request, $status)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
        if ($orders->count() > 0) {
            return ApiController::respondWithSuccess(\App\Http\Resources\Order::collection($orders));
        } else {
            if ($status == '0') {
                $errors = [
                    'key' => 'user_orders',
                    'value' => trans('messages.noNewOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            } elseif ($status == '1') {
                $errors = [
                    'key' => 'user_orders',
                    'value' => trans('messages.noActiveOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            } elseif ($status == '2') {
                $errors = [
                    'key' => 'user_orders',
                    'value' => trans('messages.noFinishedOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            } elseif ($status == '3') {
                $errors = [
                    'key' => 'user_orders',
                    'value' => trans('messages.noCanceledOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            } else {
                $errors = [
                    'key' => 'user_orders',
                    'value' => 'Wrong Url status is 0 , 1 , 2 , 3',
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }

        }
    }

    public function driver_orders(Request $request, $status)
    {
        $orders = DriverOrder::with('order')
            ->where('driver_id', $request->user()->id)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
        if ($orders->count() > 0) {
            return ApiController::respondWithSuccess(\App\Http\Resources\Order::collection($orders));
        } else {
            if ($status == '0') {
                $errors = [
                    'key' => 'driver_orders',
                    'value' => trans('messages.noNewOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            } elseif ($status == '1') {
                $errors = [
                    'key' => 'driver_orders',
                    'value' => trans('messages.noHoldOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            } elseif ($status == '2') {
                $errors = [
                    'key' => 'driver_orders',
                    'value' => trans('messages.noActiveOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            } elseif ($status == '3') {
                $errors = [
                    'key' => 'driver_orders',
                    'value' => trans('messages.noFinishedOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            } elseif ($status == '4') {
                $errors = [
                    'key' => 'driver_orders',
                    'value' => trans('messages.noCanceledOrders'),
                ];
                return ApiController::respondWithErrorArray(array($errors));
            }
//            return ApiController::respondWithErrorArray(array($errors));
        }
    }

    public function user_cancel_order(Request $request, $id)
    {
        $order = Order::find($id);
        if ($order) {
            if ($order->status == '0') {
                $order->update([
                    'status' => '3'
                ]);
                $driver_orders = DriverOrder::whereOrderId($order->id)->get();
                if ($driver_orders->count() > 0) {
                    foreach ($driver_orders as $driver_order) {
                        $driver_order->delete();
                    }
                }
                $success = [
                    'key' => 'user_cancel_order',
                    'value' => trans('messages.order_canceled_successfully')
                ];
                return ApiController::respondWithSuccess($success);
            } else {
                $errors = [
                    'key' => 'user_cancel_order',
                    'value' => trans('messages.canNotCancelOrder')
                ];
                return ApiController::respondWithErrorAuthArray(array($errors));
            }
        } else {
            $errors = [
                'key' => 'user_cancel_order',
                'value' => trans('messages.order_not_found')
            ];
            return ApiController::respondWithErrorAuthArray(array($errors));
        }
    }

}
