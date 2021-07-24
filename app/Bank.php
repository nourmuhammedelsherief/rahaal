<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'banks';
    protected $fillable = [
        'ar_name',
        'en_name',
        'ur_name',
        'account_number',
        'IBAN_number'
    ];
}
