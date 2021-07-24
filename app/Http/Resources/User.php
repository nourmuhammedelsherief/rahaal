<?php

namespace App\Http\Resources;

use App\Rate;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class User extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $locale = $request->header('Content-Language');
        if ($this->type == 1)
        {
            // User
            $rates = Rate::where('to_user_id' , $this->id)->get();
            $sum = 0;
            $average = 0;
            if ($rates->count() > 0){
                foreach ($rates as $rate)
                {
                    $sum += $rate->rate;
                }
                $average = $sum / $rates->count();
            }
            return [
                'id'            =>$this->id,
                'name'          =>$this->name,
                'country'       =>new Country($this->country),
//                'email'         =>$this->email,
                'phone_number'  =>$this->phone_number,
                'active'        =>intval($this->active),
                'type'          =>intval($this->type),
                'rate'          => intval($average),
                'photo'         =>$this->photo == null ? asset('/uploads/users/default.png') : asset('/uploads/users/'.$this->photo),
                'api_token'     =>$this->api_token,
                'created_at'    =>$this->created_at->format('Y-m-d'),
            ];
        }elseif ($this->type == 2)
        {
            // Driver
            $rates = Rate::where('to_user_id' , $this->id)->get();
            $sum = 0;
            $average = 0;
            if ($rates->count() > 0){
                foreach ($rates as $rate)
                {
                    $sum += $rate->rate;
                }
                $average = $sum / $rates->count();
            }
            return [
                'id'            =>$this->id,
                'name'          =>$this->name,
                'country'       =>new Country($this->country),
                'phone_number'  =>$this->phone_number,
                'latitude'      =>$this->latitude,
                'longitude'     =>$this->longitude,
                'active'        =>intval($this->active),
                'type'          =>intval($this->type),
                'rate'          => intval($average),
                'photo'         =>$this->photo == null ? asset('/uploads/users/default.png') : asset('/uploads/users/'.$this->photo),
                'api_token'     =>$this->api_token,
                'created_at'    =>$this->created_at->format('Y-m-d'),
            ];
        }
    }
}
