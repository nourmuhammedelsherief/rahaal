<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    protected $fillable = [
        'ar_name',
        'en_name',
        'ur_name',
        'code',
        'ar_currency',
        'en_currency',
        'ur_currency',
    ];
    public function users()
    {
        return $this->hasMany(User::class , 'country_id');
    }
}
