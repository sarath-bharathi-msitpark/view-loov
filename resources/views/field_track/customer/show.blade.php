@extends('field_track.layouts.fieldTrack')

@section('page-title')
    {{ __('Customers') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/field_coustomer.svg') }}
@endsection

@section('content')
    @include('field_track.layouts.partials.nav')

    <div class="col-12 entire_box1 mb-5">
        <div class="container">
            <h2 class="mb-4">Customer Details</h2>

            <div class="card mb-0">
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Info -->
                        <div class="col-12 mb-4">
                            <div class="row">
                                <h3 class="w-100 text-center mb-4">Basic Info</h3>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Customer Name:</label>
                                    <div><strong>{{ $customer->name }}</strong> (ID: {{ $customer->customer_id }})</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Email:</label>
                                    <div>{{ $customer->email }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Contact:</label>
                                    <div>{{ $customer->contact }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Active:</label>
                                    <div>{{ $customer->is_active ? 'Yes' : 'No' }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Purchase Manager:</label>
                                    <div>{{ $customer->purchase_manager_name }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Google Business Link:</label>
                                    <div><a href="{{ $customer->google_business_link }}"
                                            target="_blank">{{ $customer->google_business_link }}</a></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Branch Details:</label>
                                    <div>{{ $customer->branch_details }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Info -->
                        <div class="col-12 mb-4">
                            <div class="row">
                                <h3 class="w-100 text-center mb-4">Billing Info</h3>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Name:</label>
                                    <div>{{ $customer->billing_name }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Country:</label>
                                    <div>{{ $customer->billingCountry->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">State:</label>
                                    <div>{{ $customer->billingState->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">City:</label>
                                    <div>{{ $customer->billingCity->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Area:</label>
                                    <div>{{ $customer->billingArea->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Beat:</label>
                                    <div>{{ $customer->billingBeat->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Phone:</label>
                                    <div>{{ $customer->billing_phone }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Zip:</label>
                                    <div>{{ $customer->billing_zip }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Address:</label>
                                    <div>{{ $customer->billing_address }}</div>
                                </div>

                            </div>
                        </div>

                        <!-- Shipping Info -->
                        <div class="col-12 mb-4">
                            <div class="row">
                                <h3 class="w-100 text-center mb-4">Shipping Info</h3>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Name:</label>
                                    <div>{{ $customer->shipping_name }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Country:</label>
                                    <div>{{ $customer->shippingCountry->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">State:</label>
                                    <div>{{ $customer->shippingState->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">City:</label>
                                    <div>{{ $customer->shippingCity->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Area:</label>
                                    <div>{{ $customer->shippingArea->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Beat:</label>
                                    <div>{{ $customer->shippingBeat->name ?? '-' }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Phone:</label>
                                    <div>{{ $customer->shipping_phone }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Zip:</label>
                                    <div>{{ $customer->shipping_zip }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Address:</label>
                                    <div>{{ $customer->shipping_address }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="row">
                                <h3 class="w-100 text-center mb-4">Location</h3>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Latitude:</label>
                                    <div>{{ $customer->latitude }}</div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="fw-bold text-muted mb-1">Longitude:</label>
                                    <div>{{ $customer->longitude }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-0 d-flex justify-content-end mb-md-0 mb-3">
                <a href="{{ route('fieldTrack.customer.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
@endsection

@push('script-page')

@endpush

