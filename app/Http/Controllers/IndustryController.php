<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use Illuminate\Http\Request;
use App\Http\Requests\StoreIndustryRequest;
use App\Http\Requests\UpdateIndustryRequest;

class IndustryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $industries = Industry::paginate(10);
        return view('industries.index', compact('industries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('industries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIndustryRequest $request)
    {
        Industry::create($request->validated());
        return redirect()->route('industries.index')->with('success', 'Đã thêm ngành nghề thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Industry $industry)
    {
        return view('industries.show', compact('industry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Industry $industry)
    {
        return view('industries.edit', compact('industry'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIndustryRequest $request, Industry $industry)
    {
        $industry->update($request->validated());
        return redirect()->route('industries.index')->with('success', 'Đã cập nhật ngành nghề thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Industry $industry)
    {
        $industry->delete();
        return redirect()->route('industries.index')->with('success', 'Đã xóa ngành nghề thành công.');
    }
}
