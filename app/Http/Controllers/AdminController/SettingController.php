<?php

namespace App\Http\Controllers\AdminController;

use App\Electronic_wallet;
use App\History;
use App\Setting;
use App\UserDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Redirect;
use Image;
use Auth;
use App\Permission;

class SettingController extends Controller
{
    //
    public function index()
    {
            $settings =settings();
            return view('admin.settings.index',compact('settings'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'search_range'=> 'required|numeric',
        ]);

        Setting::where('id',1)->first()->update($request->all());

        return Redirect::back()->with('success', 'تم حفظ البيانات بنجاح');
    }

    public function drivers_commission(Request $request)
    {
        $this->validate($request , [
            'drivers_commission'   => 'required',
        ]);
        // update drivers_commission value
        $setting = Setting::find(1);
        $setting->update([
            'drivers_commission'  => $request->drivers_commission,
        ]);
        flash('تم تعديل  نسبة  اعموله  للسائقين')->success();
        return \redirect()->back();
    }
    public function pulls()
    {
        $pulls = Electronic_wallet::where('pull_request' , '1')->get();
        return view('admin.settings.pulls' , compact('pulls'));
    }
    public function commissions()
    {
        $commissions = Electronic_wallet::where('checked_amount' ,'!=', 0)
            ->where('commission_photo' , '!=' , null)
            ->get();
        return view('admin.settings.commissions' , compact('commissions'));
    }
    public function CommissionDone($id)
    {
        $wallet = Electronic_wallet::findOrFail($id);
        $amount = $wallet->checked_amount;
        History::create([
            'user_id'  => $wallet->user_id,
            'ar_title' => 'لقد قمت بدفع قيمة العمولة المستحقة',
            'en_title' => 'I have paid the commission due',
            'ur_title' => 'میں نے کمیشن کی وجہ سے ادائیگی کردی ہے',
            'price'    => $wallet->checked_amount,
        ]);
        $wallet->update([
            'commission_photo'   => null,
            'checked_amount'  => 0 ,
            'amount'   => 0,
        ]);
        // send notify  to user
        $ar_title = 'العمولات';
        $en_title = 'Commissions';
        $ur_title = 'کمیشن';
        $ar_message = 'تم دفع عمولتك  المستحقة بنجاح';
        $en_message = 'Your commission has been paid successfully';
        $ur_message = 'آپ کے کمیشن کی کامیابی کے ساتھ ادائیگی کردی گئی ہے';
        $devicesTokens =  UserDevice::where('user_id',$wallet->user_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        if ($devicesTokens) {
            sendMultiNotification($ar_title, $ar_message ,$devicesTokens);
        }
        saveNotification($wallet->user_id, $ar_title,$en_title,$ur_title, $ar_message ,$en_message,$ur_message,'5' , null , null);
        flash('تم التأكد  من  عمولة  السائق بنجاح')->success();
        return \redirect()->back();
    }
    public function CommissionNotDone($id)
    {
        $wallet = Electronic_wallet::findOrFail($id);
        History::create([
            'user_id'  => $wallet->user_id,
            'ar_title' => 'تم الغاء عملية دفع العمولة من قبل الأدراة',
            'en_title' => 'Commission payment has been cancelled',
            'ur_title' => 'کمیشن کی ادائیگی منسوخ کردی گئی ہے',
            'price'    => $wallet->checked_amount,
        ]);
        $wallet->update([
            'commission_photo'   => null,
            'checked_amount'  => 0 ,
        ]);
        // send notify  to user
        $ar_title = 'العمولات';
        $en_title = 'Commissions';
        $ur_title = 'کمیشن';
        $ar_message = 'تم الغاء عملية دفع العمولة من قبل الأدراة';
        $en_message = 'Commission payment has been cancelled';
        $ur_message = 'کمیشن کی ادائیگی منسوخ کردی گئی ہے';
        $devicesTokens =  UserDevice::where('user_id',$wallet->user_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        if ($devicesTokens) {
            sendMultiNotification($ar_title, $ar_message ,$devicesTokens);
        }
        saveNotification($wallet->user_id, $ar_title,$en_title,$ur_title, $ar_message ,$en_message,$ur_message,'5' , null , null);
        flash('تم الغاء عملية دفع  العمولة للسائق')->success();
        return \redirect()->back();

    }
    public function PullDone($id){
        $wallet = Electronic_wallet::findOrFail($id);
        $wallet->update([
            'pull_request' => '0',
            'cash'      => 0.0,
        ]);
        flash('تمت العمليه بنجاح')->success();
        return \redirect()->back();
    }
    public function wallet_charging()
    {
        $wallets = Electronic_wallet::where('payment_photo' , '!=' , null)
            ->where('checked_amount' , '!=' , 0)
            ->get();
        return view('admin.settings.wallet_charging' , compact('wallets'));
    }
    public function chargeDone($id)
    {
        $wallet = Electronic_wallet::findOrFail($id);
        $amount = $wallet->amount + $wallet->checked_amount;
        History::create([
            'user_id'  => $wallet->user_id,
            'ar_title' => 'لقد قمت بشحن رصيد في محفظتك الألكترونية',
            'en_title' => 'You have charged a balance in your e-wallet',
            'ur_title' => 'آپ نے اپنے ای والٹ میں بیلنس وصول کیا ہے',
            'price'    => $wallet->checked_amount,
        ]);
        $wallet->update([
            'payment_photo'   => null,
            'checked_amount'  => 0 ,
            'amount'          => $amount,
        ]);
        // send notify  to user
        $ar_title = 'المحفظه الألكترونيه';
        $en_title = 'electronic wallet';
        $ur_title = 'الیکٹرانک پرس';
        $ar_message = 'تمت عمليه أضافه الرصيد الي محفظتك الألكترونيه بنجاح من قبل الأدراة';
        $en_message = 'balance Added to Your E-Wallet Successfully From administration';
        $ur_message = 'آپ کے الیکٹرانک پرس میں توازن شامل کرنے کا عمل انتظامیہ نے کامیابی کے ساتھ مکمل کرلیا ہے';
        $devicesTokens =  UserDevice::where('user_id',$wallet->user_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        if ($devicesTokens) {
            sendMultiNotification($ar_title, $ar_message ,$devicesTokens);
        }
        saveNotification($wallet->user_id, $ar_title,$en_title,$ur_title, $ar_message ,$en_message,$ur_message,'4' , null , null);
        flash('تم أضافة الرصيد الي المحفظه الألكترونيه بنجاح')->success();
        return \redirect()->back();
    }
    public function chargeNotDone($id)
    {
        $wallet = Electronic_wallet::findOrFail($id);
        History::create([
            'user_id'  => $wallet->user_id,
            'ar_title' => 'تم الغاء عملية شحن الرصيد الي محفظتك الألكترونية من قبل الأدراة',
            'en_title' => 'The process of recharging the balance to your e-wallet has been canceled by the administration',
            'ur_title' => 'انتظامیہ نے آپ کے ای بٹوے میں بیلنس کو دوبارہ چارج کرنے کا عمل منسوخ کردیا ہے',
            'price'    => $wallet->checked_amount,
        ]);
        $wallet->update([
            'payment_photo'   => null,
            'checked_amount'  => 0 ,
        ]);
        // send notify  to user
        $ar_title = 'المحفظه الألكترونيه';
        $en_title = 'electronic wallet';
        $ur_title = 'الیکٹرانک پرس';
        $ar_message = 'تم الغاء عملية شحن الرصيد الي محفظتك الألكترونية من قبل الأدراة';
        $en_message = 'The process of recharging the balance to your e-wallet has been canceled by the administration';
        $ur_message = 'انتظامیہ نے آپ کے ای بٹوے میں بیلنس کو دوبارہ چارج کرنے کا عمل منسوخ کردیا ہے';
        $devicesTokens =  UserDevice::where('user_id',$wallet->user_id)
            ->get()
            ->pluck('device_token')
            ->toArray();
        if ($devicesTokens) {
            sendMultiNotification($ar_title, $ar_message ,$devicesTokens);
        }
        saveNotification($wallet->user_id, $ar_title,$en_title,$ur_title, $ar_message ,$en_message,$ur_message,'4' , null , null);
        flash('تم الغاء عمليةالشحن')->success();
        return \redirect()->back();
    }

}
