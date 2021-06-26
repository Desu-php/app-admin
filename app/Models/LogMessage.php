<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogMessage extends Model
{
    use HasFactory;

    const SUCCESS = 1;
    const FAILS = 0;

    protected $fillable = [
        'message_id',
        'count',
        'status',
        'user_id',
        'lid'
    ];
}
