<?php

namespace App\Http\Controllers;

use App\Models\BreakType;
use Illuminate\Http\Request;

class BreakController extends Controller
{
    public function index()
    {
        $breaks = BreakType::all();
        
        \Log::info($breaks);
        return view('breaks.index', compact('breaks'));
        
        
    }

    public function create()
    {
        return view('breaks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'break_name' => 'required|string|max:255',
            'maximum_break_time' => 'required|integer|min:1',
            'status' => 'required|boolean',
        ]);

        BreakType::create($request->all());

        return redirect()->route('breaks.index')->with('success', 'Break created successfully.');
    }

    public function edit(BreakType $break)
    {
        return view('breaks.edit', compact('break'));
    }

    public function update(Request $request, BreakType $break)
    {
        $request->validate([
            'break_name' => 'required|string|max:255',
            'maximum_break_time' => 'required|integer|min:1',
            'status' => 'required|boolean',
        ]);

        $break->update($request->all());

        return redirect()->route('breaks.index')->with('success', 'Break updated successfully.');
    }

    public function destroy(BreakType $break)
    {
        $break->delete();

        return redirect()->route('breaks.index')->with('success', 'Break deleted successfully.');
    }
}
