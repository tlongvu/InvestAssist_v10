<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Exchange;
use App\Models\Industry;
use App\Models\UserExchangeBalance;
use Illuminate\Http\Request;
use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateStockRequest;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'symbol');
        $direction = $request->get('direction', 'asc');
        $hideZero = $request->boolean('hide_zero', false);

        $query = Stock::where('user_id', auth()->id())
            ->with(['exchange', 'industry']);

        if ($hideZero) {
            $query->where('quantity', '>', 0);
        }

        // Sort within each exchange group
        switch ($sort) {
            case 'quantity':
                $query->orderBy('quantity', $direction);
                break;
            case 'invested':
                $query->orderByRaw('(quantity * avg_price) ' . $direction);
                break;
            case 'current_value':
                $query->orderByRaw('(quantity * current_price) ' . $direction);
                break;
            case 'profit':
                $query->orderByRaw('((quantity * current_price) - (quantity * avg_price)) ' . $direction);
                break;
            case 'symbol':
            default:
                $query->orderBy('symbol', $direction);
                break;
        }

        // Group by exchange (no pagination — each exchange is its own section)
        $stocks = $query->get();
        $stocksByExchange = $stocks->groupBy(fn($s) => optional($s->exchange)->name ?? 'Chưa xác định');

        $balances = \App\Models\UserExchangeBalance::where('user_id', auth()->id())
            ->with('exchange')
            ->get()
            ->mapWithKeys(function ($balance) {
                return [optional($balance->exchange)->name => $balance->balance];
            });

        return view('stocks.index', compact('stocksByExchange', 'sort', 'direction', 'hideZero', 'balances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $exchanges = Exchange::all();
        $industries = Industry::all();
        return view('stocks.create', compact('exchanges', 'industries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockRequest $request)
    {
        $stock = Stock::create(array_merge($request->validated(), ['user_id' => auth()->id()]));

        // Auto-deduct exchange balance
        $this->adjustExchangeBalance($stock, -($stock->quantity * $stock->avg_price));

        return redirect()->route('stocks.index')->with('success', 'Đã thêm cổ phiếu thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        $this->authorizeOwnership($stock);
        return view('stocks.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock)
    {
        $this->authorizeOwnership($stock);
        $exchanges = Exchange::all();
        $industries = Industry::all();
        return view('stocks.edit', compact('stock', 'exchanges', 'industries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStockRequest $request, Stock $stock)
    {
        $this->authorizeOwnership($stock);

        // Reverse old invested amount
        $oldInvested = $stock->quantity * $stock->avg_price;
        $this->adjustExchangeBalance($stock, $oldInvested);

        $stock->update($request->validated());

        // Apply new invested amount
        $newInvested = $stock->quantity * $stock->avg_price;
        $this->adjustExchangeBalance($stock, -$newInvested);

        return redirect()->route('stocks.index')->with('success', 'Đã cập nhật cổ phiếu thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock)
    {
        $this->authorizeOwnership($stock);

        // Restore balance before deleting
        $invested = $stock->quantity * $stock->avg_price;
        $this->adjustExchangeBalance($stock, $invested);

        $stock->delete();
        return redirect()->route('stocks.index')->with('success', 'Đã xóa cổ phiếu thành công.');
    }

    /**
     * Kiểm tra quyền sở hữu – 403 nếu không phải của user hiện tại.
     */
    private function authorizeOwnership(Stock $stock): void
    {
        if ($stock->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }
    }

    /**
     * Adjust exchange balance by a delta amount.
     */
    private function adjustExchangeBalance(Stock $stock, float $delta): void
    {
        if (!$stock->exchange_id || $delta == 0) return;

        $balance = UserExchangeBalance::firstOrCreate(
            ['user_id' => $stock->user_id, 'exchange_id' => $stock->exchange_id],
            ['balance' => 0]
        );

        $balance->increment('balance', $delta);
    }
}
