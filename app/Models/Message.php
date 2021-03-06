<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'messageId',
        'chatType',
        'chatId',
        'channelId',
        'authorType',
        'dateTime',
        'type',
        'status',
        'text',
        'authorName',
        'content'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
