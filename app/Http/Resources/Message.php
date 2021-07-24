<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Message extends JsonResource
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
            'id'            =>$this->id,
            'room_id' => intval($this->conversation_id),
            'user_id'  => $this->user_id,
            'message' => $this->message,
            'file' => $this->file,
            'created_at'    => $this->created_at->format('Y-m-d'),
        ];
    }
}
