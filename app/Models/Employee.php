<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'user_wazzup',
        'position',
        'wazzup_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
