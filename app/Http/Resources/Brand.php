<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Brand extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $lang = $request->header('Content-Language');
        return [
            'id'   => $this->id,
            'name'       => $lang == 'en' ? $this->en_name : ($lang == 'ur' ? $this->ur_name : $this->ar_name),
        ];
    }
}
