<?php

namespace App\Console\Commands;

use App\Electronic_wallet;
use App\ElectronicPocket;
use App\Setting;
use App\UserDevice;
use Illuminate\Console\Command;

class CommissionLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissionLimit:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'commission Check every day and if the commission Large than the limit make the user un active';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('commission Check every day and if the commission Large than the limit make the user un active');
        $commissionLimit = Setting::find(1)->commission_limit;
        $wallets = Electronic_wallet::where('amount' , '<=' , -$commissionLimit )->get();
        if ($wallets->count() > 0)
        {
            foreach ($wallets as $wallet)
            {
                $wallet->user->update([
                    'active' => 0
                ]);
                // send Notification to user
                $ar_title = 'العمولات';
                $en_title = 'Commissions';
                $ur_title = 'کمیشن';
                $ar_message = 'تم وصولك للحد الأقصي  من  العمولات يجب عليك دفع عمولة التطبيق';
                $en_message = 'You have reached the maximum amount of commissions, you must pay the application commission';
                $ur_message = 'آپ کمیشنوں کی زیادہ سے زیادہ رقم کو پہنچ چکے ہیں ، آپ کو درخواست کمیشن کو ادائیگی کرنا ہوگی';
                $devicesTokens =  UserDevice::where('user_id' , $wallet->user->id)
                    ->get()
                    ->pluck('device_token')
                    ->toArray();
                if ($devicesTokens) {
                    sendMultiNotification($ar_title, $ar_message ,$devicesTokens);
                }
                saveNotification($wallet->user->id, $ar_title,$en_title,$ur_title, $ar_message ,$en_message,$ur_message,'4' , null , null);
            }
        }
    }
}
