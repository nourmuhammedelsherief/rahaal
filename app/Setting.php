<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
    protected $fillable=[
        'search_range',
        'bearer_token',
        'sender_name',
        'driver_commission',
        'order_limit',
        'account_number',
        'bank_name',
        'commission_limit',
        'contact_number'
    ];
}
