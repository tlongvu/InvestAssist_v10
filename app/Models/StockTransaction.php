<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'stock_id',
        'type',
        'quantity',
        'price',
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

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
