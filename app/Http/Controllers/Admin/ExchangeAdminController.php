<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exchange;
use Illuminate\Http\Request;

class ExchangeAdminController extends Controller
{
    public function index()
    {
        $exchanges = Exchange::orderBy('name')->paginate(20);
        return view('admin.exchanges.index', compact('exchanges'));
    }

    public function create()
    {
        return view('admin.exchanges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:exchanges,name'],
        ]);

        Exchange::create(['name' => $request->name]);

        return redirect()->route('admin.exchanges.index')
            ->with('success', 'Đã thêm công ty chứng khoán thành công.');
    }

    public function edit(Exchange $exchange)
    {
        return view('admin.exchanges.edit', compact('exchange'));
    }

    public function update(Request $request, Exchange $exchange)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:exchanges,name,' . $exchange->id],
        ]);

        $exchange->update(['name' => $request->name]);

        return redirect()->route('admin.exchanges.index')
            ->with('success', 'Đã cập nhật công ty chứng khoán thành công.');
    }

    public function destroy(Exchange $exchange)
    {
        $exchange->delete();

        return redirect()->route('admin.exchanges.index')
            ->with('success', 'Đã xóa công ty chứng khoán thành công.');
    }
}
