<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $table = 'conversations';
    protected $fillable = [
        'first',
        'second',
        'first_online',
        'second_online',
        'seen',
        'block',
        'block_maker',
        'block_reason',
        'order_id',
        'status'
    ];
    public function the_first()
    {
        return $this->belongsTo(User::class , 'first');
    }
    public function the_second()
    {
        return $this->belongsTo(User::class , 'second');
    }
    public function maker()
    {
        return $this->belongsTo(User::class , 'block_maker');
    }
}
