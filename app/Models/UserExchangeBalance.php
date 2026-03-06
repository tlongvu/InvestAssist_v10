<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExchangeBalance extends Model
{
    protected $fillable = ['user_id', 'exchange_id', 'balance'];

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
