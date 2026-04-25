<?php

namespace App\Http\Controllers\LandingPageApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Utility;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactUsMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ContactUsApiController extends Controller
{
    public function contactUs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'work_email' => 'required|email',
            'company_name' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'users' => 'nullable|string|max:50',
            'referral' => 'nullable|string|max:255',
            'message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        try {

            // Mail::to(['jeeva.msitpark@gmail.com'])->send(new ContactUsMail($data));
            Mail::to('support@loov.site')
                // ->cc(['example1@email.com', 'example2@email.com'])
                ->send(new ContactUsMail($data));


            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your message has been sent.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your message has been received.',
            ]);
        }
    }
}

