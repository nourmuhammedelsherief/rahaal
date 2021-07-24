<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleBrand extends Model
{
    protected $table = 'vehicle_brands';
    protected $fillable = [
        'ar_name',
        'en_name',
        'ur_name',
    ];
}
