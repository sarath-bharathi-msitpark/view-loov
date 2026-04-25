<?php

namespace App\Http\Controllers\Web\FieldTrack;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Beat;
use App\Models\City;
use App\Models\Country;
use App\Models\Customer;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $creatorId = Auth::user()->creatorId();

        $countries = Country::where('created_by', $creatorId)->get();
        $states = State::where('created_by', $creatorId)->get();
        $cities = City::where('created_by', $creatorId)->get();
        $areas = Area::where('created_by', $creatorId)->get();
        $beats = Beat::where('created_by', $creatorId)->get();

        $query = Customer::with([
            'billingCountry',
            'billingState',
            'billingCity',
            'billingArea',
            'billingBeat'
        ])->where('created_by', $creatorId);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('contact', 'like', "%{$searchTerm}%");
            });
        }

        $filters = [
            'country' => ['billing_country', 'shipping_country'],
            'state' => ['billing_state', 'shipping_state'],
            'city' => ['billing_city', 'shipping_city'],
            'area' => ['billing_area', 'shipping_area'],
            'beat' => ['billing_beat', 'shipping_beat'],
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

        $perPage = $request->input('per_page', 10);
        $customers = $query->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends($request->query());

        return view('field_track.customer.index', compact(
            'customers', 'countries', 'states', 'cities', 'areas', 'beats'
        ));
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
        $customer = Customer::with('billingCountry', 'billingState', 'billingCity', 'billingArea', 'billingBeat',
            'shippingCountry', 'shippingState', 'shippingCity', 'shippingArea', 'shippingBeat')->findOrFail($id);

        return view('field_track.customer.show', compact('customer'));
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
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->back()->with('success', 'Customer deleted successfully.');
    }
}
