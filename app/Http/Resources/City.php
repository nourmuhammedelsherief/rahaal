<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class City extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        return
        $this->collection->transform(function($page , $locale){
           return [
                    'id' => $page->id,
                    'ar_name' => $page->ar_name,
                    'en_name' => $page->en_name,
                    'created_at' => $page->created_at->format('Y-m-d')
                ];
            });
    }
}
