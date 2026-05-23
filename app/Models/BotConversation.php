<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotConversation extends Model
{
    protected $fillable = [
        'phone_number',
        'sender',
        'message',
        'intent'
    ];
}
