<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $table = 'trucks';
    protected $fillable = [
        'user_id',
        'truck_type_id',
        'vehicle_brand_id',
        'model_year',
        'plate_number',
        'maximum_round',
        'id_photo',
        'car_form',
        'driver_license',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
    public function truck_type()
    {
        return $this->belongsTo(TruckType::class , 'truck_type_id');
    }
    public function vehicle_brand()
    {
        return $this->belongsTo(VehicleBrand::class , 'vehicle_brand_id');
    }
}
