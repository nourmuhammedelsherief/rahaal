<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Country extends JsonResource
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
            'id'         => $this->id,
            'name'       => $lang == 'en' ? $this->en_name : ($lang == 'ur' ? $this->ur_name : $this->ar_name),
            'currency'   => $lang == 'en' ? $this->en_currency : ($lang == 'ur' ? $this->ur_currency : $this->ar_currency),
            'code'       => $this->code,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
