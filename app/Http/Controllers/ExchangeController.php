<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use App\Models\UserExchangeBalance;
use Illuminate\Http\Request;
use App\Http\Requests\StoreExchangeRequest;
use App\Http\Requests\UpdateExchangeRequest;

class ExchangeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exchanges = Exchange::paginate(10);
        $allExchanges = Exchange::all();

        // Lấy số dư hiện tại của user theo từng sàn (dạng map: exchange_id => balance)
        $userBalances = UserExchangeBalance::where('user_id', auth()->id())
            ->pluck('balance', 'exchange_id');

        return view('exchanges.index', compact('exchanges', 'allExchanges', 'userBalances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('exchanges.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExchangeRequest $request)
    {
        Exchange::create($request->validated());
        return redirect()->route('exchanges.index')->with('success', 'Đã thêm sàn giao dịch thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exchange $exchange)
    {
        return view('exchanges.show', compact('exchange'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exchange $exchange)
    {
        return view('exchanges.edit', compact('exchange'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExchangeRequest $request, Exchange $exchange)
    {
        $exchange->update($request->validated());
        return redirect()->route('exchanges.index')->with('success', 'Đã cập nhật sàn giao dịch thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exchange $exchange)
    {
        $exchange->delete();
        return redirect()->route('exchanges.index')->with('success', 'Đã xóa sàn giao dịch thành công.');
    }

    /**
     * Cập nhật số dư tiền mặt nhập tay theo từng sàn của user hiện tại.
     */
    public function updateBalances(Request $request)
    {
        $request->validate([
            'balances'   => ['required', 'array'],
            'balances.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        foreach ($request->balances as $exchangeId => $balance) {
            if ($balance === null || $balance === '') continue;

            UserExchangeBalance::updateOrCreate(
                ['user_id' => auth()->id(), 'exchange_id' => $exchangeId],
                ['balance' => $balance]
            );
        }

        return back()->with('success', 'Đã cập nhật số dư tài khoản thành công.');
    }
}
