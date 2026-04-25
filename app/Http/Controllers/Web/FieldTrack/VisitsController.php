<?php

namespace App\Http\Controllers\Web\FieldTrack;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $customers = Customer::where('created_by', $creatorId)->get();
        $employees = Employee::where('created_by', $creatorId)
            ->where('is_active', true)
            ->whereHas('user', function ($query) {
                $query->where('type', 'Employee')
                    ->where('track_type', USER_APK_TYPE_FIELD_TRACK);
            })
            ->get();

        try {
            $query = Visit::with(['area', 'beat', 'customer', 'employee'])
                ->where('creator_id', $creatorId);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('area', fn($q2) => $q2->where('name', 'like', "%$search%"))
                        ->orWhereHas('beat', fn($q2) => $q2->where('name', 'like', "%$search%"))
                        ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%$search%"))
                        ->orWhere('description', 'like', "%$search%");
                });
            }

            if ($request->filled('customer')) {
                $query->where('customer_id', $request->customer);
            }

            if ($request->filled('employee')) {
                $query->where('employee_id', $request->employee);
            }

            $start = $end = null;
            if ($request->filled('date_range')) {
                $dateRange = $request->date_range;

                if (strpos($dateRange, ' to ') !== false) {
                    [$start, $end] = explode(' to ', $dateRange);
                } elseif (strpos($dateRange, ' - ') !== false) {
                    [$start, $end] = explode(' - ', $dateRange);
                } elseif (strlen($dateRange) === 10) {
                    $start = $end = $dateRange;
                }

                if ($start && $end) {
                    $query->whereBetween('visit_date', [
                        Carbon::parse(trim($start))->startOfDay(),
                        Carbon::parse(trim($end))->endOfDay()
                    ]);
                }
            } else {
                $query->whereBetween('visit_date', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
            }

            $perPage = $request->get('per_page', 10);
            $visits = $query->orderByDesc('visit_date')
                ->paginate($perPage)
                ->appends($request->query());

            $visits->getCollection()->transform(function ($visit) {
                if ($visit->image) {
                    $visit->image = \App\Models\Utility::get_file($visit->image);
                }
                return $visit;
            });

            return view('field_track.visit.index', compact('visits', 'employees', 'customers'));

        } catch (\Exception $e) {
            \Log::error('Visit index failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
