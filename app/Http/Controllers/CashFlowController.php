<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use App\Models\Exchange;
use App\Models\UserExchangeBalance;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCashFlowRequest;
use App\Http\Requests\UpdateCashFlowRequest;

class CashFlowController extends Controller
{
    public function index()
    {
        $cashFlows = CashFlow::where('user_id', auth()->id())
            ->with('exchange')
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);
        return view('cash_flows.index', compact('cashFlows'));
    }

    public function create()
    {
        $exchanges = Exchange::all();
        return view('cash_flows.create', compact('exchanges'));
    }

    public function store(StoreCashFlowRequest $request)
    {
        $cashFlow = CashFlow::create(array_merge($request->validated(), ['user_id' => auth()->id()]));

        // Auto-update exchange balance
        $this->adjustExchangeBalance($cashFlow);

        // Cộng dồn vào tất cả snapshot lịch sử
        $this->adjustAssetHistory($cashFlow);

        // Send Telegram Notification
        $typeVi = $cashFlow->type === 'deposit' ? 'Nạp tiền' : 'Rút tiền';
        $icon = $cashFlow->type === 'deposit' ? '💰' : '💸';

        $cashFlow->load('exchange');
        $exchangeName = $cashFlow->exchange ? $cashFlow->exchange->name : 'N/A';
        $date = \Carbon\Carbon::parse($cashFlow->transaction_date)->format('d/m/Y');

        $message = "{$icon} *GIAO DỊCH DÒNG TIỀN*\n\n"
                 . "Loại: *{$typeVi}*\n"
                 . "Số tiền: *" . number_format($cashFlow->amount) . " VND*\n"
                 . "Sàn: *{$exchangeName}*\n"
                 . "Ngày: *{$date}*";

        \App\Services\TelegramService::sendMessage(auth()->user(), $message);

        return redirect()->route('cash-flows.index')->with('success', 'Đã ghi nhận biến động tiền thành công.');
    }

    public function show(CashFlow $cashFlow)
    {
        $this->authorizeOwnership($cashFlow);
        return view('cash_flows.show', compact('cashFlow'));
    }

    public function edit(CashFlow $cashFlow)
    {
        $this->authorizeOwnership($cashFlow);
        $exchanges = Exchange::all();
        return view('cash_flows.edit', compact('cashFlow', 'exchanges'));
    }

    public function update(UpdateCashFlowRequest $request, CashFlow $cashFlow)
    {
        $this->authorizeOwnership($cashFlow);

        // Reverse old adjustments
        $this->reverseExchangeBalance($cashFlow);
        $this->reverseAssetHistory($cashFlow);

        $cashFlow->update($request->validated());

        // Apply new adjustments
        $this->adjustExchangeBalance($cashFlow);
        $this->adjustAssetHistory($cashFlow);

        return redirect()->route('cash-flows.index')->with('success', 'Đã cập nhật biến động tiền thành công.');
    }

    public function destroy(CashFlow $cashFlow)
    {
        $this->authorizeOwnership($cashFlow);

        // Reverse adjustments before deleting
        $this->reverseExchangeBalance($cashFlow);
        $this->reverseAssetHistory($cashFlow);

        $cashFlow->delete();
        return redirect()->route('cash-flows.index')->with('success', 'Đã xóa biến động tiền thành công.');
    }

    private function authorizeOwnership(CashFlow $cashFlow): void
    {
        if ($cashFlow->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }
    }

    /**
     * Adjust exchange balance: +deposit, -withdrawal
     */
    private function adjustExchangeBalance(CashFlow $cashFlow): void
    {
        if (!$cashFlow->exchange_id) return;

        $balance = UserExchangeBalance::firstOrCreate(
            ['user_id' => $cashFlow->user_id, 'exchange_id' => $cashFlow->exchange_id],
            ['balance' => 0]
        );

        $delta = $cashFlow->type === 'deposit' ? $cashFlow->amount : -$cashFlow->amount;
        $balance->increment('balance', $delta);
    }

    /**
     * Reverse the balance effect of a cash flow (used before update/delete)
     */
    private function reverseExchangeBalance(CashFlow $cashFlow): void
    {
        if (!$cashFlow->exchange_id) return;

        $balance = UserExchangeBalance::where('user_id', $cashFlow->user_id)
            ->where('exchange_id', $cashFlow->exchange_id)
            ->first();

        if ($balance) {
            $delta = $cashFlow->type === 'deposit' ? -$cashFlow->amount : $cashFlow->amount;
            $balance->increment('balance', $delta);
        }
    }

    /**
     * Cộng dồn deposit / trừ withdrawal vào TẤT CẢ snapshot lịch sử
     */
    private function adjustAssetHistory(CashFlow $cashFlow): void
    {
        $delta = $cashFlow->type === 'deposit' ? $cashFlow->amount : -$cashFlow->amount;

        AssetHistory::where('user_id', $cashFlow->user_id)
            ->increment('total_value', $delta);
    }

    /**
     * Đảo ngược hiệu ứng cộng dồn (dùng khi update/delete)
     */
    private function reverseAssetHistory(CashFlow $cashFlow): void
    {
        $delta = $cashFlow->type === 'deposit' ? -$cashFlow->amount : $cashFlow->amount;

        AssetHistory::where('user_id', $cashFlow->user_id)
            ->increment('total_value', $delta);
    }
}
