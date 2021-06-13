<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Whatsapp extends Model
{
    use HasFactory;

    const DISABLED = 0;
    const ENABLED = 1;

    protected $fillable = [
        'username',
        'api_key',
        'wazzup_id',
        'status',
        'user_id',
        'whatsapp',
        'channelId'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
