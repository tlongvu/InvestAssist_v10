<?php

namespace App\Models;

use App\Models\Industry;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'symbol',
        'exchange_id',
        'industry_id',
        'quantity',
        'avg_price',
        'current_price',
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

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
