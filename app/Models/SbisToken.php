<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SbisToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'sbis_account_id',
        'access_token',
        'sid',
        'token',
    ];
}
