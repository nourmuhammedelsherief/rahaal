<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    //
    protected $table='rates';
    protected $fillable=[
        'from_user_id',
        'to_user_id',
        'rate',
    ];
}
