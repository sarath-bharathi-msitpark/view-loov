<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignationController extends Controller
{
    public function index(Request $request): Factory|View|Application|RedirectResponse
    {
        $user  = Auth::user();
        $query = Designation::where('created_by', $user->creatorId());

        // ── Search: name OR description ──────────────────────────────────────
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('description', 'like', $term);
            });
        }

        // ── Date Range Filter (single "date_range" param: "2025-01-01 to 2025-03-31") ──
        if ($request->filled('date_range')) {
            $parts = array_map('trim', explode(' to ', $request->date_range));
            if (count($parts) === 2) {
                $query->whereDate('created_at', '>=', $parts[0])
                      ->whereDate('created_at', '<=', $parts[1]);
            } elseif (count($parts) === 1 && $parts[0]) {
                $query->whereDate('created_at', $parts[0]);
            }
        }

        // ── Stats: always from full unfiltered dataset ───────────────────────
        $baseQuery = Designation::where('created_by', $user->creatorId());

        $thisMonthCount = (clone $baseQuery)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastSixMonthsCount = (clone $baseQuery)
            ->where('created_at', '>=', now()->subMonths(6)->startOfDay())
            ->count();

        // ── Paginate ─────────────────────────────────────────────────────────
        $perPage      = (int) $request->get('per_page', 10);
        $designations = $query->orderBy('id', 'desc')->paginate($perPage);
        $designations->appends($request->except('page'));

        return view('company.settings.designation', compact(
            'designations',
            'thisMonthCount',
            'lastSixMonthsCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
        ], [
            'name.required'        => 'Name is required.',
            'name.string'          => 'Name must be a valid string.',
            'name.max'             => 'Name must not exceed 255 characters.',
            'description.required' => 'Description is required.',
            'description.string'   => 'Description must be a valid string.',
        ]);

        Designation::create([
            'name'        => $request->name,
            'description' => $request->description,
            'created_by'  => Auth::user()->creatorId() ?? Auth::id(),
        ]);

        return redirect()->route('organization.settings.designation')
            ->with('success', 'Designation created successfully.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
        ], [
            'name.required'        => 'Name is required.',
            'name.string'          => 'Name must be a valid string.',
            'name.max'             => 'Name must not exceed 255 characters.',
            'description.required' => 'Description is required.',
            'description.string'   => 'Description must be a valid string.',
        ]);

        $designation = Designation::findOrFail($id);
        $designation->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('organization.settings.designation')
            ->with('success', 'Designation updated successfully.');
    }
}