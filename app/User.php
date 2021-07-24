<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPUnit\Framework\Constraint\Count;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',  'password',
        'phone_number',
        'photo',
        'api_token',
        'active',
        'verification_code',
        'email',
        'type',     // 1- user  2- driver
        'country_id',
        'invoice_id',
        'latitude',
        'longitude',
        'availability'

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function country()
    {
        return $this->belongsTo(Country::class , 'country_id');
    }
    public function driver_orders()
    {
        return $this->hasMany(DriverOrder::class , 'driver_id');
    }
    public function trucks()
    {
        return $this->hasMany(Truck::class , 'user_id');
    }
}
