<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'histories';
    protected $fillable = [
        'user_id',
        'ar_title',
        'en_title',
        'ur_title',
        'price'
    ];
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
}
