<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $fillable = [
        'user_id',
        'type',
        'ar_message',
        'en_message',
        'ur_message',
        'ar_title',
        'en_title',
        'ur_title',
        'order_id',
        'offer_id',
        'is_read',
    ];
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class , 'order_id');
    }
}
