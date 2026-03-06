<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Xóa unique constraint cũ chỉ có (symbol, exchange_id)
            $table->dropUnique('stocks_symbol_exchange_id_unique');

            // Thêm unique constraint mới bao gồm user_id
            // Mỗi user có thể có cùng mã cổ phiếu trên cùng sàn
            $table->unique(['user_id', 'symbol', 'exchange_id'], 'stocks_user_symbol_exchange_unique');
        });
    }

    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropUnique('stocks_user_symbol_exchange_unique');
            $table->unique(['symbol', 'exchange_id']);
        });
    }
};
