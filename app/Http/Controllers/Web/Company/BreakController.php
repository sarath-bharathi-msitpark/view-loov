<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\BreakType;
use Illuminate\Support\Facades\Auth;

class BreakController extends Controller
{
    /**
     * List all breaks with search, status filter, pagination, and stat counts.
     */
    public function index(Request $request): Factory|View|Application|RedirectResponse
    {
        $user = Auth::user();

        if (!$user->can('break_report')) {
            return redirect()->back()->with('error', 'Permission denied.');
        }

        $query = BreakType::where('created_by', $user->creatorId());

        // ── Search ──────────────────────────────────────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('break_name', 'like', '%' . $search . '%');
                if (strtolower($search) === 'active') {
                    $q->orWhere('status', 1);
                } elseif (strtolower($search) === 'inactive') {
                    $q->orWhere('status', 0);
                }
            });
        }

        // ── Status Dropdown Filter ────────────────────────────────────────
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', (int) $request->status);
        }

        // ── Stat counts (always from full dataset, not filtered) ─────────────
        $baseQuery     = BreakType::where('created_by', $user->creatorId());
        $activeCount   = (clone $baseQuery)->where('status', 1)->count();
        $inactiveCount = (clone $baseQuery)->where('status', 0)->count();
        $limitCount    = (clone $baseQuery)->where('break_limit_apply', 1)->count();

        // ── Paginate ─────────────────────────────────────────────────────────
        $perPage = $request->get('per_page', 10);
        $breaks  = $query->orderBy('id', 'desc')->paginate($perPage);
        $breaks->appends($request->except('page'));

        return view('company.settings.break', compact(
            'breaks',
            'activeCount',
            'inactiveCount',
            'limitCount'
        ));
    }

    /**
     * Store a new break.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'break_name'         => 'required|string|max:255',
            'maximum_break_time' => 'required|integer|min:1',
            'break_limit_apply'  => 'required|boolean',
            'status'             => 'required|boolean',
        ], [
            'break_name.required'         => 'Break name is required.',
            'break_name.string'           => 'Break name must be a valid string.',
            'break_name.max'              => 'Break name must not exceed 255 characters.',
            'maximum_break_time.required' => 'Maximum break time is required.',
            'maximum_break_time.integer'  => 'Maximum break time must be a number.',
            'maximum_break_time.min'      => 'Maximum break time must be at least 1 minute.',
            'break_limit_apply.required'  => 'Please specify whether break limit applies.',
            'break_limit_apply.boolean'   => 'Invalid value for break limit apply.',
            'status.required'             => 'Status is required.',
            'status.boolean'              => 'Invalid status value.',
        ]);

        BreakType::create([
            'break_name'         => $request->break_name,
            'maximum_break_time' => $request->maximum_break_time,
            'break_limit_apply'  => $request->break_limit_apply,
            'status'             => $request->status,
            'created_by'         => auth()->user()->creatorId() ?? auth()->id(),
        ]);

        return redirect()->route('organization.settings.break')
            ->with('success', 'Break created successfully.');
    }

    /**
     * Show edit form (used for non-AJAX fallback).
     */
    public function edit(int $id): Factory|View|Application
    {
        $break  = BreakType::findOrFail($id);
        $breaks = BreakType::where('created_by', Auth::user()->creatorId())->paginate(10);

        return view('company.settings.break', compact('break', 'breaks'));
    }

    /**
     * Update an existing break.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'break_name'         => 'required|string|max:255',
            'maximum_break_time' => 'required|integer|min:1',
            'break_limit_apply'  => 'required|boolean',
            'status'             => 'required|boolean',
        ], [
            'break_name.required'         => 'Break name is required.',
            'break_name.string'           => 'Break name must be a valid string.',
            'break_name.max'              => 'Break name must not exceed 255 characters.',
            'maximum_break_time.required' => 'Maximum break time is required.',
            'maximum_break_time.integer'  => 'Maximum break time must be a number.',
            'maximum_break_time.min'      => 'Maximum break time must be at least 1 minute.',
            'break_limit_apply.required'  => 'Please specify whether break limit applies.',
            'break_limit_apply.boolean'   => 'Invalid value for break limit apply.',
            'status.required'             => 'Status is required.',
            'status.boolean'              => 'Invalid status value.',
        ]);

        $break = BreakType::findOrFail($id);
        $break->update($request->only([
            'break_name',
            'maximum_break_time',
            'break_limit_apply',
            'status',
        ]));

        return redirect()->route('organization.settings.break')
            ->with('success', 'Break updated successfully.');
    }

    /**
     * Delete a break.
     */
    public function destroy(BreakType $break): RedirectResponse
    {
        $break->delete();

        return redirect()->route('organization.settings.break')
            ->with('success', 'Break deleted successfully.');
    }
}