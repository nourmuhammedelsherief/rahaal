<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Offer extends JsonResource
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
            'id'               => $this->id,
            'driver'           => $this->driver_id == null ? null : new User($this->driver),
            'price'            => $this->price,
            'status'           => $this->status,
            'order'            => new Order($this->order),
            'order_distance'   => distanceBetweenTowPlaces($this->order->latitude_from , $this->order->longitude_from , $this->order->latitude_to , $this->order->longitude_to),
            'driver_order_dis' => $this->driver == null ? null : distanceBetweenTowPlaces($this->driver->latitude , $this->driver->longitude , $this->order->latitude_from , $this->order->longitude_from),
            'created_at'       => $this->created_at->DiffForHumans(),
        ];
    }
}
