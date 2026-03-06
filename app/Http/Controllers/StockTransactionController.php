<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Http\Requests\StoreStockTransactionRequest;
use App\Http\Requests\UpdateStockTransactionRequest;

class StockTransactionController extends Controller
{
    public function index()
    {
        $stockTransactions = StockTransaction::whereHas('stock', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->with('stock.exchange')
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);
        return view('stock_transactions.index', compact('stockTransactions'));
    }

    public function create()
    {
        $stocks = Stock::where('user_id', auth()->id())->with('exchange')->get();
        return view('stock_transactions.create', compact('stocks'));
    }

    public function store(StoreStockTransactionRequest $request)
    {
        $transaction = StockTransaction::create($request->validated());

        // Send Telegram Notification
        $transaction->load('stock.exchange');
        $stock = $transaction->stock;

        $typeVi = $transaction->type == 'buy' ? 'Mua' : 'Bán';
        $icon = $transaction->type == 'buy' ? '📈' : '📉';
        $exchangeName = optional($stock->exchange)->name ?? 'N/A';
        $date = \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y');

        $message = "{$icon} *KHỚP LỆNH CHỨNG KHOÁN*\n\n"
                 . "Lệnh: *{$typeVi} {$stock->symbol}*\n"
                 . "Số lượng: *" . number_format($transaction->quantity) . "*\n"
                 . "Giá khớp: *" . number_format($transaction->price) . " VND*\n"
                 . "Tổng giá trị: *" . number_format($transaction->quantity * $transaction->price) . " VND*\n"
                 . "Sàn: *{$exchangeName}*\n"
                 . "Ngày: *{$date}*";

        \App\Services\TelegramService::sendMessage(auth()->user(), $message);

        return redirect()->route('stock-transactions.index')->with('success', 'Đã ghi nhận giao dịch thành công.');
    }

    public function show(StockTransaction $stockTransaction)
    {
        return view('stock_transactions.show', compact('stockTransaction'));
    }

    public function edit(StockTransaction $stockTransaction)
    {
        $stocks = Stock::where('user_id', auth()->id())->with('exchange')->get();
        return view('stock_transactions.edit', compact('stockTransaction', 'stocks'));
    }

    public function update(UpdateStockTransactionRequest $request, StockTransaction $stockTransaction)
    {
        $stockTransaction->update($request->validated());
        return redirect()->route('stock-transactions.index')->with('success', 'Đã cập nhật giao dịch thành công.');
    }

    public function destroy(StockTransaction $stockTransaction)
    {
        $stockTransaction->delete();
        return redirect()->route('stock-transactions.index')->with('success', 'Đã xóa giao dịch thành công.');
    }
}
