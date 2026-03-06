<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
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

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class);
    }
}
