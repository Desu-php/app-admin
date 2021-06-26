<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sbis extends Model
{
    use HasFactory;

    protected $fillable = [
        'sbislidid',
        'chatId'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class, 'chatId', 'chatId');
    }
}
