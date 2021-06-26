<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SbisAccount extends Model
{
    use HasFactory;

    const ENABLED = 1;
    const DISABLED = 0;

    protected $fillable = [
        'app_client_id',
        'app_secret',
        'secret_key',
        'status',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function token()
    {
        return $this->hasOne(SbisToken::class);
    }
}
