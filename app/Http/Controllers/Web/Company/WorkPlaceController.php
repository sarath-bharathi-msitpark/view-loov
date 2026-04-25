<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkPlace;

class WorkPlaceController extends Controller
{
    public function index()
    {
        return view('company.settings.workplace');
    }

    public function update(Request $request)
    {
        $request->validate([
            'min_hours_half_day' => 'required|numeric|min:0|max:24',
            'min_hours_full_day' => 'required|numeric|min:0|max:24',
        ]);

        $userId = auth()->id(); // Or get the relevant user/company ID

        $workplace = WorkPlace::where('user_id', $userId)->first();

        if (!$workplace) {
            $workplace = new WorkPlace();
            $workplace->user_id = $userId;
        }

        // Calculate the difference between full day and half day hours
        $workplaceMinHalfDay = $request->min_hours_full_day - $request->min_hours_half_day; // 18 - 6 = 12
        $workplaceMinFullDay = 24 - $request->min_hours_full_day; // 24 - 18 = 6

        // Update the workplace settings
        $workplace->workplace_max_hours_for_absent = $request->min_hours_half_day; // 6
        $workplace->workplace_min_hours_for_half_day = $workplaceMinHalfDay; // 12
        $workplace->workplace_min_hours_for_full_day = $workplaceMinFullDay; // 6

        // Save the changes to the database
        $workplace->save();

        return back()->with('success', 'Workplace settings updated successfully.');
    }
}
