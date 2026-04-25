<?php

namespace App\Http\Controllers\StealthAPI;

use App\Http\Controllers\Controller;
use App\Models\ApplicationLog;
use App\Models\Incident;
use App\Models\User;
use App\Models\Utility;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function routineEvent(Request $request)
    {
        $companyId = $request->input('company_id');
        $company = User::findOrFail($companyId);
        $authUser = auth()->user();

        $companySlug = Str::slug($company->company_name);
        $nameSlug = Str::slug($authUser->name);

        $validator = Validator::make($request->all(), [
            'screenshot' => 'required|file|mimes:jpg,jpeg,png',
            'keyboard_action_count' => 'required|integer|min:0',
            'mouse_action_count' => 'required|integer|min:0',
            'capture_date_and_time' => 'required|date_format:Y-m-d H:i:s',
            'requested_date_and_time' => 'required|date_format:Y-m-d H:i:s',
            'application_log' => 'required|array|min:1',
            'application_log.*.application_name_or_url' => 'required|string',
            'application_log.*.screen_time' => ['required', 'regex:/^([0-1]\d|2[0-3]):([0-5]\d):([0-5]\d)$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $screenshotUrl = null;
            if ($request->hasFile('screenshot')) {
                $filenameWithExt = $request->file('screenshot')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('screenshot')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $dir = "uploads/companies/{$companySlug}/routine-event/{$nameSlug}/screenshots";

                $image_path = $dir . '/' . $filenameWithExt;
                if (\File::exists($image_path)) {
                    \File::delete($image_path);
                }

                $path = Utility::upload_file($request, 'screenshot', $fileNameToStore, $dir, []);


                if ($path['flag'] == 1) {
                    $screenshotUrl = $path['url'];
                } else {
                    return response()->json([
                        'is_success' => false,
                        'message' => $path['msg']
                    ], 400);
                }
            }

            $incident = Incident::create([
                'user_id' => $authUser->id,
                'screenshot' => $screenshotUrl,
                'keyboard_action_count' => $request->input('keyboard_action_count', 0),
                'mouse_action_count' => $request->input('mouse_action_count', 0),
                'capture_date_and_time' => $request->input('capture_date_and_time'),
                'requested_date_and_time' => $request->input('requested_date_and_time'),
            ]);

            foreach ($request->input('application_log') as $appLog) {
                $applicationNameOrUrl = $appLog['application_name_or_url'];
                $screenTime = $appLog['screen_time'];

                $isUrl = filter_var($applicationNameOrUrl, FILTER_VALIDATE_URL);

                $applicationName = $isUrl
                    ? parse_url($applicationNameOrUrl, PHP_URL_HOST)
                    : $applicationNameOrUrl;

                ApplicationLog::create([
                    'user_id' => $authUser->id,
                    'incident_id' => $incident->id,
                    'application_name' => $applicationName,
                    'url' => $isUrl ? $applicationNameOrUrl : null,
                    'screen_time' => $screenTime,
                ]);
            }

            DB::commit();

            return response()->json([
                'is_success' => true,
                'message' => 'Event Created Successfully',
                'data' => $incident,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Routine Event Creation Failed: ' . $e->getMessage());

            return response()->json([
                'is_success' => false,
                'message' => 'Failed to create event.',
            ], 500);
        }
    }
}
