<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table='offers';
    protected $fillable = [
        'driver_id',
        'order_id',
        'status',
        'price',

    ];
    public function driver()
    {
        return $this->belongsTo(User::class , 'driver_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class , 'order_id');
    }
}
