<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Place extends JsonResource
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
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'user'        => $this->user->name,
            'name'        => $this->name,
            'description' => $this->description,
            'latitude'    => $this->latitude,
            'longitude'   => $this->longitude,
            'created_at'  => $this->created_at->diffForHumans(),
        ];
    }
}
