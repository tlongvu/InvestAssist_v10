<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telegram_enabled',
        'telegram_bot_token',
        'telegram_chat_id',
        'bank_interest_rate',
    ];

    /**
     * Kiểm tra user có phải admin không.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'telegram_enabled' => 'boolean',
        'bank_interest_rate' => 'decimal:2',
    ];

    /**
     * Quan hệ: User có nhiều stocks.
     */
    public function stocks()
    {
        return $this->hasMany(\App\Models\Stock::class);
    }

    /**
     * Quan hệ: User có nhiều cash flows.
     */
    public function cashFlows()
    {
        return $this->hasMany(\App\Models\CashFlow::class);
    }
}
