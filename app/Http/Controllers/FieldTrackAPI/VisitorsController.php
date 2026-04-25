<?php

namespace App\Http\Controllers\FieldTrackAPI;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Utility;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VisitorsController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Visit::with(['area', 'beat', 'customer', 'employee'])
                ->where('creator_id', Auth::user()->creatorId());

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('area', fn($q2) => $q2->where('name', 'like', "%$search%"))
                        ->orWhereHas('beat', fn($q2) => $q2->where('name', 'like', "%$search%"))
                        ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%$search%"))
                        ->orWhere('description', 'like', "%$search%");
                });
            }

            if ($request->filled('visit_date')) {
                $query->whereDate('visit_date', $request->visit_date);
            }

            $perPage = $request->get('per_page', 10);
            $visits = $query->orderByDesc('visit_date')->paginate($perPage);

            $visits->getCollection()->transform(function ($visit) {
                if ($visit->image) {
                    $visit->image = \App\Models\Utility::get_file($visit->image);
                }
                return $visit;
            });

            return response()->json([
                'is_success' => true,
                'message' => 'Visit list',
                'data' => $visits->items(),
                'pagination' => [
                    'current_page' => $visits->currentPage(),
                    'per_page' => $visits->perPage(),
                    'total' => $visits->total(),
                    'last_page' => $visits->lastPage(),
                    'next_page_url' => $visits->nextPageUrl(),
                    'prev_page_url' => $visits->previousPageUrl(),
                ],
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Visit index failed', ['error' => $e->getMessage()]);
            return response()->json([
                'is_success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area_id' => 'required|exists:areas,id',
            'beat_id' => 'required|exists:beats,id',
            'customer_id' => 'required|exists:customers,id',
            'visit_date' => 'required|date',
            'visit_time' => 'required|date_format:H:i:s',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->visit_date && $request->visit_time) {
                $visitDateTime = \Carbon\Carbon::parse($request->visit_date . ' ' . $request->visit_time);
                if ($visitDateTime->lessThanOrEqualTo(now())) {
                    $validator->errors()->add(
                        'visit_date',
                        'Visit date and time must be greater than the current date and time.'
                    );
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->first(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $imageUrl = null;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $dir = "uploads/visits/{$request->customer_id}/images";

                // Ensure directory exists
                if (!\File::exists(public_path($dir))) {
                    \File::makeDirectory(public_path($dir), 0755, true);
                }

                $path = Utility::upload_file($request, 'image', $fileNameToStore, $dir, []);
                if ($path['flag'] == 1) {
                    $imageUrl = $path['url']; // Full URL
                } else {
                    return response()->json([
                        'is_success' => false,
                        'message' => $path['msg']
                    ], 400);
                }
            }

            $employee = Employee::where('user_id', Auth::user()->id)->firstOrFail();

            $visit = Visit::create([
                'area_id' => $request->area_id,
                'beat_id' => $request->beat_id,
                'customer_id' => $request->customer_id,
                'employee_id' => $employee->id,
                'creator_id' => Auth::user()->creatorId(),
                'visit_date' => $request->visit_date,
                'visit_time' => $request->visit_time,
                'description' => $request->description,
                'image' => $imageUrl,
            ]);

            DB::commit();

            return response()->json([
                'is_success' => true,
                'message' => 'Visit created successfully',
                'data' => $visit,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Visit creation failed', ['error' => $e->getMessage()]);

            return response()->json([
                'is_success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $visit = Visit::with(['area', 'beat', 'customer', 'employee'])->find($id);

        if (!$visit) {
            return response()->json([
                'is_success' => false,
                'message' => 'Visit not found.',
            ], 404);
        }

        if ($visit->image) {
            $visit->image = url($visit->image);
        }

        return response()->json([
            'is_success' => true,
            'message' => 'Visit details',
            'data' => $visit,
        ], 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $visit = Visit::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'area_id' => 'required|exists:areas,id',
            'beat_id' => 'required|exists:beats,id',
            'customer_id' => 'required|exists:customers,id',
            'visit_date' => 'required|date',
            'visit_time' => 'required|date_format:H:i:s',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $imageUrl = $visit->image;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $dir = "uploads/visits/{$request->customer_id}/images";

                // Ensure directory exists
                if (!\File::exists(public_path($dir))) {
                    \File::makeDirectory(public_path($dir), 0755, true);
                }

                // Delete old image if exists
                if ($visit->image && \File::exists(public_path(parse_url($visit->image, PHP_URL_PATH)))) {
                    \File::delete(public_path(parse_url($visit->image, PHP_URL_PATH)));
                }

                $path = Utility::upload_file($request, 'image', $fileNameToStore, $dir, []);
                if ($path['flag'] == 1) {
                    $imageUrl = $path['url']; // Full URL
                } else {
                    return response()->json([
                        'is_success' => false,
                        'message' => $path['msg']
                    ], 400);
                }
            }

            $employee = Employee::where('user_id', Auth::user()->id)->firstOrFail();

            $visit->update([
                'area_id' => $request->area_id,
                'beat_id' => $request->beat_id,
                'customer_id' => $request->customer_id,
                'employee_id' => $employee->id,
                'creator_id' => Auth::user()->creatorId(),
                'visit_date' => $request->visit_date,
                'visit_time' => $request->visit_time,
                'description' => $request->description,
                'image' => $imageUrl,
            ]);

            DB::commit();

            return response()->json([
                'is_success' => true,
                'message' => 'Visit updated successfully',
                'data' => $visit,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Visit update failed', ['error' => $e->getMessage()]);

            return response()->json([
                'is_success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $visit = Visit::find($id);

            if (!$visit) {
                return response()->json([
                    'is_success' => false,
                    'message' => 'Visit not found.',
                ], 404);
            }

            if ($visit->image && \File::exists(public_path($visit->image))) {
                \File::delete(public_path($visit->image));
            }

            $visit->delete();

            return response()->json([
                'is_success' => true,
                'message' => 'Visit deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Visit delete failed', ['error' => $e->getMessage()]);

            return response()->json([
                'is_success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

}
