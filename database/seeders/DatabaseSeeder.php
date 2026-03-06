<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin user
        $admin = User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        // 2. Create Normal user
        $user = User::factory()->create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => bcrypt('password'),
            'role'     => 'user',
        ]);

        // 3. Create Exchanges (dùng chung)
        $hose  = \App\Models\Exchange::create(['name' => 'HOSE']);
        $hnx   = \App\Models\Exchange::create(['name' => 'HNX']);
        $upcom = \App\Models\Exchange::create(['name' => 'UPCOM']);

        // 4. Create Industries (dùng chung)
        $banking    = \App\Models\Industry::create(['name' => 'Banking']);
        $tech       = \App\Models\Industry::create(['name' => 'Technology']);
        $realestate = \App\Models\Industry::create(['name' => 'Real Estate']);
        $retail     = \App\Models\Industry::create(['name' => 'Retail']);

        // 5. Cash Flows của Test User
        \App\Models\CashFlow::create([
            'user_id'          => $user->id,
            'exchange_id'      => $hose->id,
            'type'             => 'deposit',
            'amount'           => 500000000, // 500M
            'transaction_date' => \Carbon\Carbon::now()->subMonths(6),
        ]);

        \App\Models\CashFlow::create([
            'user_id'          => $user->id,
            'exchange_id'      => $hnx->id,
            'type'             => 'deposit',
            'amount'           => 200000000, // 200M
            'transaction_date' => \Carbon\Carbon::now()->subMonths(3),
        ]);

        // 6. Stocks và Transactions của Test User

        // Stock 1: FPT (Tech, HOSE)
        $fpt = \App\Models\Stock::create([
            'user_id'       => $user->id,
            'symbol'        => 'FPT',
            'exchange_id'   => $hose->id,
            'industry_id'   => $tech->id,
            'quantity'      => 2000,
            'avg_price'     => 95000,
            'current_price' => 115000,
        ]);

        \App\Models\StockTransaction::create([
            'stock_id'         => $fpt->id,
            'type'             => 'buy',
            'quantity'         => 1000,
            'price'            => 90000,
            'transaction_date' => \Carbon\Carbon::now()->subMonths(5),
        ]);

        \App\Models\StockTransaction::create([
            'stock_id'         => $fpt->id,
            'type'             => 'buy',
            'quantity'         => 1000,
            'price'            => 100000,
            'transaction_date' => \Carbon\Carbon::now()->subMonths(2),
        ]);

        // Stock 2: VCB (Banking, HOSE)
        $vcb = \App\Models\Stock::create([
            'user_id'       => $user->id,
            'symbol'        => 'VCB',
            'exchange_id'   => $hose->id,
            'industry_id'   => $banking->id,
            'quantity'      => 3000,
            'avg_price'     => 85000,
            'current_price' => 82000,
        ]);

        \App\Models\StockTransaction::create([
            'stock_id'         => $vcb->id,
            'type'             => 'buy',
            'quantity'         => 3000,
            'price'            => 85000,
            'transaction_date' => \Carbon\Carbon::now()->subMonths(4),
        ]);

        // Stock 3: MWG (Retail, HOSE)
        $mwg = \App\Models\Stock::create([
            'user_id'       => $user->id,
            'symbol'        => 'MWG',
            'exchange_id'   => $hose->id,
            'industry_id'   => $retail->id,
            'quantity'      => 5000,
            'avg_price'     => 45000,
            'current_price' => 52000,
        ]);

        \App\Models\StockTransaction::create([
            'stock_id'         => $mwg->id,
            'type'             => 'buy',
            'quantity'         => 5000,
            'price'            => 45000,
            'transaction_date' => \Carbon\Carbon::now()->subMonths(1),
        ]);

        // Stock 4: CEO (Real Estate, HNX)
        $ceo = \App\Models\Stock::create([
            'user_id'       => $user->id,
            'symbol'        => 'CEO',
            'exchange_id'   => $hnx->id,
            'industry_id'   => $realestate->id,
            'quantity'      => 10000,
            'avg_price'     => 22000,
            'current_price' => 22500,
        ]);

        \App\Models\StockTransaction::create([
            'stock_id'         => $ceo->id,
            'type'             => 'buy',
            'quantity'         => 10000,
            'price'            => 22000,
            'transaction_date' => \Carbon\Carbon::now()->subWeeks(2),
        ]);
    }
}
