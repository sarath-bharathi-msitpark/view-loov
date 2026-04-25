<?php

namespace App\Http\Controllers\FieldTrackAPI;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Customer::with([
                'billingCountry', 'billingState', 'billingCity', 'billingArea', 'billingBeat',
                'shippingCountry', 'shippingState', 'shippingCity', 'shippingArea', 'shippingBeat',
            ])->where('created_by', Auth::user()->creatorId());

            // Search by text
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%")
                        ->orWhere('contact', 'like', "%{$searchTerm}%");
                });
            }

            $filters = [
                'country_id' => ['billing_country', 'shipping_country'],
                'state_id' => ['billing_state', 'shipping_state'],
                'city_id' => ['billing_city', 'shipping_city'],
                'area_id' => ['billing_area', 'shipping_area'],
                'beat_id' => ['billing_beat', 'shipping_beat'],
            ];

            foreach ($filters as $requestKey => $columns) {
                if ($request->filled($requestKey)) {
                    $value = $request->$requestKey;
                    $query->where(function ($q) use ($columns, $value) {
                        foreach ($columns as $column) {
                            $q->orWhere($column, $value);
                        }
                    });
                }
            }

            $customers = $query->orderByDesc('created_at')->paginate(10);

            return response()->json([
                'is_success' => true,
                'message' => 'Customer List',
                'data' => $customers->items(),
                'pagination' => [
                    'current_page' => $customers->currentPage(),
                    'first_page_url' => $customers->url(1),
                    'from' => $customers->firstItem(),
                    'last_page' => $customers->lastPage(),
                    'last_page_url' => $customers->url($customers->lastPage()),
                    'next_page_url' => $customers->nextPageUrl(),
                    'path' => $customers->url($customers->currentPage()),
                    'per_page' => $customers->perPage(),
                    'prev_page_url' => $customers->previousPageUrl(),
                    'to' => $customers->lastItem(),
                    'total' => $customers->total(),
                ],
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Customer index error', ['error' => $e->getMessage()]);

            return response()->json([
                'is_success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    function customerNumber()
    {
        $latest = Customer::latest()->first();
        if (!$latest) {
            return 1;
        }
        $nextCustomerId = $latest->customer_id + 1;

        while (Customer::where('customer_id', $nextCustomerId)->exists()) {
            $nextCustomerId++;
        }

        return $nextCustomerId;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'email' => 'required|email|unique:customers,email',
            'billing_name' => 'required|string|max:255',
            'billing_country' => 'required|exists:countries,id',
            'billing_state' => 'required|exists:states,id',
            'billing_city' => 'required|exists:cities,id',
            'billing_area' => 'nullable|exists:areas,id',
            'billing_beat' => 'nullable|exists:beats,id',
            'billing_phone' => 'nullable|string|max:20',
            'billing_zip' => 'nullable|string|max:10',
            'billing_address' => 'nullable|string|max:500',
            'shipping_name' => 'nullable|string|max:255',
            'shipping_country' => 'nullable|exists:countries,id',
            'shipping_state' => 'nullable|exists:states,id',
            'shipping_city' => 'nullable|exists:cities,id',
            'shipping_area' => 'nullable|exists:areas,id',
            'shipping_beat' => 'nullable|exists:beats,id',
            'shipping_phone' => 'nullable|string|max:20',
            'shipping_zip' => 'nullable|string|max:10',
            'shipping_address' => 'nullable|string|max:500',
            'purchase_manager_name' => 'nullable|string|max:255',
            'google_business_link' => 'nullable|url',
            'branch_details' => 'nullable|string|max:500',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'profile.max' => 'The profile image must not exceed 2MB in size.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $customerData = $request->only([
                'name', 'contact', 'email',
                'billing_name', 'billing_country', 'billing_state', 'billing_city', 'billing_area', 'billing_beat', 'billing_phone', 'billing_zip', 'billing_address',
                'shipping_name', 'shipping_country', 'shipping_state', 'shipping_city', 'shipping_area', 'shipping_beat', 'shipping_phone', 'shipping_zip', 'shipping_address',
                'purchase_manager_name', 'google_business_link', 'branch_details',
                'latitude', 'longitude'
            ]);

            $customerData['customer_id'] = $this->customerNumber(); // if method exists
            $customerData['created_by'] = Auth::user()->creatorId();

            // Handle profile upload as avatar
            if ($request->hasFile('profile')) {
                $filename = pathinfo($request->file('profile')->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $request->file('profile')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $settings = Utility::getStorageSetting();
                $dir = ($settings['storage_setting'] == 'local') ? 'uploads/avatar/' : 'uploads/avatar';

                $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);

                if ($path['flag'] == 1) {
                    $customerData['avatar'] = $fileNameToStore;
                } else {
                    return response()->json([
                        'is_success' => false,
                        'message' => $path['msg']
                    ], 400);
                }
            }

            $customer = Customer::create($customerData);

            return response()->json([
                'is_success' => true,
                'message' => 'Customer added successfully',
                'data' => $customer
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Customer store error', ['error' => $e->getMessage()]);

            return response()->json([
                'is_success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }


    public function show(Customer $customer)
    {
        try {
            $customer->load([
                'billingCountry', 'billingState', 'billingCity', 'billingArea', 'billingBeat',
                'shippingCountry', 'shippingState', 'shippingCity', 'shippingArea', 'shippingBeat',
            ]);

            $customer->avatar_url = $customer->avatar_url;

            return response()->json([
                'is_success' => true,
                'message' => 'Customer Details',
                'data' => $customer
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Customer show error', ['error' => $e->getMessage()]);

            return response()->json([
                'is_success' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'email' => 'required|email|unique:customers,email,' . $id,
            'billing_name' => 'required|string|max:255',
            'billing_country' => 'required|exists:countries,id',
            'billing_state' => 'required|exists:states,id',
            'billing_city' => 'required|exists:cities,id',
            'billing_area' => 'nullable|exists:areas,id',
            'billing_beat' => 'nullable|exists:beats,id',
            'billing_phone' => 'nullable|string|max:20',
            'billing_zip' => 'nullable|string|max:10',
            'billing_address' => 'nullable|string|max:500',
            'shipping_name' => 'nullable|string|max:255',
            'shipping_country' => 'nullable|exists:countries,id',
            'shipping_state' => 'nullable|exists:states,id',
            'shipping_city' => 'nullable|exists:cities,id',
            'shipping_area' => 'nullable|exists:areas,id',
            'shipping_beat' => 'nullable|exists:beats,id',
            'shipping_phone' => 'nullable|string|max:20',
            'shipping_zip' => 'nullable|string|max:10',
            'shipping_address' => 'nullable|string|max:500',
            'purchase_manager_name' => 'nullable|string|max:255',
            'google_business_link' => 'nullable|url',
            'branch_details' => 'nullable|string|max:500',
            'limit' => 'nullable|integer|min:0',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        try {
            $fields = [
                'name', 'contact', 'email',
                'billing_name', 'billing_country', 'billing_state', 'billing_city', 'billing_area', 'billing_beat', 'billing_phone', 'billing_zip', 'billing_address',
                'shipping_name', 'shipping_country', 'shipping_state', 'shipping_city', 'shipping_area', 'shipping_beat', 'shipping_phone', 'shipping_zip', 'shipping_address',
                'purchase_manager_name', 'google_business_link', 'branch_details',
                'category', 'limit', 'latitude', 'longitude'
            ];

            $customerData = collect($input)->only($fields)->toArray();
            $customerData['updated_by'] = auth()->id();

            if ($request->hasFile('profile')) {
                $file = $request->file('profile');
                $fileNameToStore = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                    . '_' . time() . '.' . $file->getClientOriginalExtension();

                $settings = Utility::getStorageSetting();
                $dir = ($settings['storage_setting'] == 'local') ? 'uploads/avatar/' : 'uploads/avatar';

                $path = Utility::upload_file($request, 'profile', $fileNameToStore, $dir, []);
                if ($path['flag'] == 1) {
                    $customerData['avatar'] = $fileNameToStore;
                } else {
                    return response()->json([
                        'is_success' => false,
                        'message' => $path['msg']
                    ], 400);
                }
            }

            $customer->update($customerData);

            return response()->json([
                'is_success' => true,
                'message' => 'Customer updated successfully',
                'data' => $customer
            ], 200);

        } catch (\Exception $e) {
            Log::error('Customer update error', ['error' => $e->getMessage()]);

            return response()->json([
                'is_success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    public function destroy(Customer $customer)
    {
        if ($customer->created_by !== Auth::user()->creatorId()) {
            return response()->json([
                'is_success' => false,
                'message' => 'Permission Denied'
            ], 403);
        }

        try {
            $customer->delete();

            return response()->json([
                'is_success' => true,
                'message' => 'Customer deleted successfully',
                'data' => null
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Customer delete error', ['error' => $e->getMessage()]);

            return response()->json([
                'is_success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }
}
