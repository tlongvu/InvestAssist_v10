<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\Request;

class IndustryAdminController extends Controller
{
    public function index()
    {
        $industries = Industry::orderBy('name')->paginate(20);
        return view('admin.industries.index', compact('industries'));
    }

    public function create()
    {
        return view('admin.industries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:industries,name'],
        ]);

        Industry::create(['name' => $request->name]);

        return redirect()->route('admin.industries.index')
            ->with('success', 'Đã thêm ngành nghề thành công.');
    }

    public function edit(Industry $industry)
    {
        return view('admin.industries.edit', compact('industry'));
    }

    public function update(Request $request, Industry $industry)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:industries,name,' . $industry->id],
        ]);

        $industry->update(['name' => $request->name]);

        return redirect()->route('admin.industries.index')
            ->with('success', 'Đã cập nhật ngành nghề thành công.');
    }

    public function destroy(Industry $industry)
    {
        $industry->delete();

        return redirect()->route('admin.industries.index')
            ->with('success', 'Đã xóa ngành nghề thành công.');
    }
}
