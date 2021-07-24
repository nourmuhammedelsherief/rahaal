<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPlace extends Model
{
    protected $table = 'user_places';
    protected $fillable = [
        'name',
        'user_id',
        'association',
        'latitude',
        'longitude',
        'description',
    ];
    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }
}
