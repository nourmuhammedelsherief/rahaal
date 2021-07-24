<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->lang = $request->header('Content-Language');
        return
            $this->collection->transform(function ($query){
                $locale = $this->lang;
                return [
                    'id'          =>$query->id,
                    'user'        =>$query->user->id,
                    'order'       =>$query->order == null ? null : $query->order->id,
                    'offer'       =>$query->offer == null ? null : $query->offer->id,
                    'title'       =>$locale == 'ar' ? $query->ar_title : ($locale == 'en' ? $query->en_title : $query->ur_title),
                    'message'     =>$locale == 'ar' ? $query->ar_message : ($locale == 'en' ? $query->en_message : $query->ur_message),
                    'type'        =>$query->type,
                    'created_at'  => $query->created_at->format('Y-m-d'),
                ];
            });
    }
}
