<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Electronic_wallet extends Model
{
    protected $table = 'electronic_wallets';
    protected $fillable = [
        'user_id',
        'amount',
        'checked_amount',
        'payment_photo',
        'commission_photo',
        'pull_request',
    ];
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
}
