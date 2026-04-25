<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    /**
     * @param Request $request
     * @return Factory|View|Application|object
     */
    public function index(Request $request)
    {
        $query = Shift::where('created_by', Auth::user()->creatorId());

        if ($request->filled('search')) {
            $query->where('shift_name', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->get('per_page', 10);

        $shifts = $query->orderBy('id', 'desc')->paginate($perPage);

        $shifts->appends($request->except('page'));

        return view('company.settings.shift', compact('shifts'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'shift_name' => 'required|string|max:191',
            'timezone' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
//            'end_time' => 'required|after:start_time',
            'grace_period' => 'required|integer|min:1',
            'max_break_time' => 'required|integer|min:1',
//            'week_off' => 'required|array|min:1',
            'week_off' => 'nullable|array',
//            'week_off.*' => 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
        ], [
            'shift_name.required' => 'Shift name is required.',
            'shift_name.string' => 'Shift name must be a string.',
            'shift_name.max' => 'Shift name must not exceed 191 characters.',

            'timezone.required' => 'Timezone is required.',
            'timezone.string' => 'Timezone must be a valid string.',

            'start_time.required' => 'Start time is required.',

            'end_time.required' => 'End time is required.',
            'end_time.after' => 'End time must be after start time.',

            'grace_period.required' => 'Grace period is required.',
            'grace_period.integer' => 'Grace period must be an integer.',
            'grace_period.min' => 'Grace period must be at least 1 minute.',

            'max_break_time.required' => 'Maximum break time is required.',
            'max_break_time.integer' => 'Maximum break time must be an integer.',
            'max_break_time.min' => 'Maximum break time must be at least 1 minute.',

            'week_off.required' => 'At least one week off day must be selected.',
            'week_off.array' => 'Week off must be an array of days.',
            'week_off.min' => 'You must select at least one day as week off.',
            'week_off.*.in' => 'Week off day must be a valid day of the week.',
        ]);

        Shift::create([
            'shift_name' => $request->shift_name,
            'timezone' => $request->timezone,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'grace_period' => $request->grace_period,
            'max_break_time' => $request->max_break_time,
            'week_off' => $request->week_off ? implode(',', $request->week_off) : null,
            'created_by' => auth()->user()->id ?? null,
        ]);

        return redirect()->route('organization.settings.shift')->with('success', 'Shift created successfully!');
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'shift_name' => 'required|string|max:191',
            'timezone' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
//            'end_time' => 'required|after:start_time',
            'grace_period' => 'required|integer|min:1',
            'max_break_time' => 'required|integer|min:1',
//            'week_off' => 'required|array|min:1',
            'week_off' => 'nullable|array',
//            'week_off.*' => 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
        ], [
            'shift_name.required' => 'Shift name is required.',
            'shift_name.string' => 'Shift name must be a string.',
            'shift_name.max' => 'Shift name must not exceed 191 characters.',

            'timezone.required' => 'Timezone is required.',
            'timezone.string' => 'Timezone must be a valid string.',

            'start_time.required' => 'Start time is required.',

            'end_time.required' => 'End time is required.',
            'end_time.after' => 'End time must be after start time.',

            'grace_period.required' => 'Grace period is required.',
            'grace_period.integer' => 'Grace period must be an integer.',
            'grace_period.min' => 'Grace period must be at least 1 minute.',

            'max_break_time.required' => 'Maximum break time is required.',
            'max_break_time.integer' => 'Maximum break time must be an integer.',
            'max_break_time.min' => 'Maximum break time must be at least 1 minute.',

            'week_off.required' => 'At least one week off day must be selected.',
            'week_off.array' => 'Week off must be an array of days.',
            'week_off.min' => 'You must select at least one day as week off.',
            'week_off.*.in' => 'Week off day must be a valid day of the week.',
        ]);

        $shift = Shift::findOrFail($id);

        $shift->update([
            'shift_name' => $request->shift_name,
            'timezone' => $request->timezone,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'grace_period' => $request->grace_period,
            'max_break_time' => $request->max_break_time,
            'week_off' => $request->week_off ? implode(',', $request->week_off) : null,
            'updated_by' => auth()->user()->id ?? null,
        ]);

        return redirect()->route('organization.settings.shift')->with('success', 'Shift updated successfully!');
    }
}
