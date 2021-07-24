<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Truck extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'user'  => new User($this->user),
            'truck_type' => new TruckType($this->truck_type),
            'vehicle_brand' => new Brand($this->vehicle_brand),
            'model_year' => $this->model_year,
            'plate_number' => $this->plate_number,
            'maximum_round' => $this->maximum_round,
            'id_photo' => $this->id_photo == null ? null : asset('/uploads/id_photos/'.$this->id_photo),
            'car_form' => $this->car_form == null ? null : asset('/uploads/car_forms/'.$this->car_form),
            'driver_license' => $this->driver_license == null ? null : asset('/uploads/driver_licenses/'.$this->driver_license),
            'created_at' => $this->created_at->format('Y-m-d')
        ];
    }
}
