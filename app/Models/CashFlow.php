<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'exchange_id',
        'type',
        'amount',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
