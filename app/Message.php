<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $fillable = [
        'conversation_id',
        'user_id',
        'message',
        'file',
    ];
    public function conversation()
    {
        return $this->belongsTo(Conversation::class , 'conversation_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
}
