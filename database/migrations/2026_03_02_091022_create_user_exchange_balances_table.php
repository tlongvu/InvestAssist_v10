<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_exchange_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exchange_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 18, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'exchange_id']); // mỗi user chỉ có 1 dòng/sàn
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_exchange_balances');
    }
};
