@php
    $layout = auth()->user()->isSuperAdmin() ? 'admin.layouts.admin' : 'company.layouts.company';
@endphp

@extends($layout)
@push('script-page')
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300,
        })
        $(".list-group-item").click(function() {
            $('.list-group-item').filter(function() {
                return this.href == id;
            }).parent().removeClass('text-primary');
        });
    </script>
@endpush

@push('css-page')
    <style>
        #card-element {
            border: 1px solid #a3afbb !important;
            border-radius: 10px !important;
            padding: 10px !important;
        }
    </style>
@endpush

@php
    $dir = asset(Storage::url('uploads/plan'));
    $dir_payment = asset(Storage::url('uploads/payments'));
@endphp
@section('page-title')
    {{ __('Manage Order Summary') }}
@endsection
@section('page-icon')
    {{ asset('assets/assestsnew/subscription.svg') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('general.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('general.plans.index') }}">{{ __('Plan') }}</a></li>
    <li class="breadcrumb-item">{{ __('Order Summary') }}</li>
@endsection

@section('content')
    @if(auth()->user()->isSuperAdmin())
        @include('admin.layouts.partials.nav')
    @else
        @include('company.layouts.partials.nav')
    @endif
    <div class="row strong_setsubscribe">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="sticky-top" style="top:30px">
                        <div class="mt-5">
                            <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.2s"
                                style="background:#fff;visibility: visible;animation-delay: 0.2s;animation-name: fadeInUp;">
                                <div class="card-body">
                                    <span class="price-badge status_badge bg-primary">{{ isset($plan->company->company_name) ? $plan->company->company_name : 'Free' }}</span>
                                    <h4 class="mb-4 f-w-600 mt-4">
                                        Plan Price: {{ isset($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '$' }}{{ number_format($plan->getTotalPlanAmount($plan->id), 2, '.', ',') }} 
                                    </h4>
                                    <ul class="list-unstyled my-5 mt-3">
                                        <li>
                                            <span class="theme-avtar me-1"><i class="text-primary ti ti-circle-plus"></i></span>
                                            {{ $plan->max_users == -1 ? __('Unlimited') : $plan->max_users }}
                                            {{ __('Users') }}
                                        </li>
                                        @forelse(json_decode($plan->features ?? '[]') as $feature)
                                        <li>
                                            <span class="theme-avtar me-1"><i class="text-primary ti ti-circle-plus"></i></span>
                                            {{ $feature }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card " style="background:#fff;">
                            <div class="list-group list-group-flush" id="useradd-sidenav">
                                @if ((\Auth::user()->payment_mode == "" || \Auth::user()->payment_mode == "bank_transfer") &&
                                    $admin_payment_setting['is_bank_transfer_enabled'] == 'on' && !empty($admin_payment_setting['bank_details']))
                                    <a href="#bank_payment"
                                        class="list-group-item list-group-item-action border-0 ">{{ __('Bank Transfer') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @endif
                                @if (
                                    (\Auth::user()->payment_mode == "" || \Auth::user()->payment_mode == "stripe") &&
                                    $admin_payment_setting['is_stripe_enabled'] == 'on' &&
                                        !empty($admin_payment_setting['stripe_key']) &&
                                        !empty($admin_payment_setting['stripe_secret']))
                                    <a href="#stripe_payment"
                                        class="list-group-item list-group-item-action border-0 ">{{ __('Stripe') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @endif
                                
                                @if (
                                    (\Auth::user()->payment_mode == "" || \Auth::user()->payment_mode == "cashfree") &&
                                    isset($admin_payment_setting['is_cashfree_enabled']) && $admin_payment_setting['is_cashfree_enabled'] == 'on')
                                    <a href="#cashfree_payment"
                                        class="list-group-item list-group-item-action border-0">{{ __('Cashfree') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-9">
                    
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Bank Transfer --}}
                    @if (
                        (\Auth::user()->payment_mode == "" || \Auth::user()->payment_mode == "bank_transfer") &&
                        $admin_payment_setting['is_bank_transfer_enabled'] == 'on' && !empty($admin_payment_setting['bank_details']))
                        <div id="bank_payment" class="card" style="background:#fff;">
                            <div class="card-header">
                                <h5 class="mb-3">{{ __('Bank Transfer') }}</h5>
                            </div>
                            <div class="tab-pane {{ ($admin_payment_setting['is_bank_transfer_enabled'] == 'on' && !empty($admin_payment_setting['bank_details'])) == 'on' ? 'active' : '' }}"
                                id="bank_payment">
                                <form role="form" action="{{ route('plan.pay.with.bank') }}" method="post"
                                    class="require-validation" id="bank-payment-form" enctype = "multipart/form-data">
                                    @csrf
                                    <div class="border p-3 rounded bank-payment-div">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div><strong>Bank Details</strong></div> <br>
                                                    @if (!empty($admin_payment_setting['bank_details'])) 
                                                         {!! nl2br(e($admin_payment_setting['bank_details'])) !!}   
                                                    @endif
                                            </div>
                                            <div class="col-md-6"> 
                                                @if (!empty($admin_payment_setting['bank_details']) && isset($admin_payment_setting['bank_upi_qr']))  
                                                <div><strong>QR Code</strong></div> <br>
                                                    @php
                                                        $path1 =  \App\Models\Utility::get_file($admin_payment_setting['bank_upi_qr']);
                                                    @endphp
                                                    <img src="{{ $path1 }}" alt="UPI QR" class="img-thumbnail" style="max-width: 44%;">
                                                @endif
                                            </div> 
                                        </div><br><br>

                                        <div class="row mt-2">
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-group w-100">
                                                        <label for="bank_coupon"
                                                            class="form-label">{{ __('Coupon') }}</label>
                                                            <div class="d-flex flex-wrap coupon_withapply">
                                                                <input type="text" id="bank_coupon" name="coupon"
                                                                    class="form-control coupon"
                                                                    placeholder="{{ __('Enter Coupon Code') }}">
                                                                <a href="#" class="text-muted apply-coupon" data-bs-toggle="tooltip"
                                                                    title="{{ __('Apply') }}">Apply
                                                                </a>
                                                            </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="bank_coupon" class="form-label">{{ __('Payment Receipt') }}</label> <x-required/>
                                                <div class="choose-file form-group">
                                                    <input type="file" name="payment_receipt" id="image"
                                                        class="form-control">
                                                    <p class="upload_file"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-4 my-4">
                                                <div class="custom-radio">
                                                    <label class="font-16 font-bold">{{ __('Net Amount') }} : </label>
                                                    <span
                                                        class="final-price">
                                                 {{ isset($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '$' }} {{ number_format($plan->total_amount, 2, '.', ',') }}</span>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <input type="submit" value="{{ __('Pay Now') }}"
                                                class="btn btn-primary mb-2 me-3">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif


                    {{-- stripe payment --}}
                    @if (
                        (\Auth::user()->payment_mode == "" || \Auth::user()->payment_mode == "stripe") &&
                        $admin_payment_setting['is_stripe_enabled'] == 'on' &&
                            !empty($admin_payment_setting['stripe_key']) &&
                            !empty($admin_payment_setting['stripe_secret']))
                        <div id="stripe_payment" class="card" style="background:#fff;">
                            <div class="card-header">
                                <h5 class="mb-3">{{ __('Stripe') }}</h5>
                            </div>
                            <div class="tab-pane {{ ($admin_payment_setting['is_stripe_enabled'] == 'on' && !empty($admin_payment_setting['stripe_key']) && !empty($admin_payment_setting['stripe_secret'])) == 'on' ? 'active' : '' }}"
                                id="stripe_payment">
                                <form role="form" action="{{ route('stripe.checkout') }}" method="post"
                                    class="require-validation" id="payment-form">
                                    @csrf
                                    <div class="border p-3 rounded stripe-payment-div">
                                        <div class="row mb-3">
                                            <div class="col-sm-8">
                                                <div class="custom-radio">
                                                    <label
                                                        class="font-16 font-weight-bold">{{ __('Credit / Debit Card') }}</label>
                                                </div>
                                                <p class="mb-0 pt-1 text-sm">
                                                    {{ __('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="billing-address" class="form-label text-dark">{{ __('Address') }}</label>
                                                    <input type="text" name="address[line1]" id="billing-address" class="form-control required"
                                                        value="{{ \Auth::user()->address }}" required>
                                                </div>
                                            </div>
                                        </div>
                                
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="billing-city" class="form-label text-dark">{{ __('City') }}</label>
                                                    <input type="text" name="address[city]" id="billing-city" value="{{ \Auth::user()->city }}" class="form-control required"
                                                        placeholder="Chennai" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="billing-state" class="form-label text-dark">{{ __('State') }}</label>
                                                    <input type="text" name="address[state]" id="billing-state" value="{{ \Auth::user()->state }}" class="form-control"
                                                        placeholder="Tamil Nadu">
                                                </div>
                                            </div>
                                        </div>
                                
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="billing-postal" class="form-label text-dark">{{ __('Postal Code') }}</label>
                                                    <input type="text" name="address[postal_code]" id="billing-postal" value="{{ \Auth::user()->postal_code }}" class="form-control required"
                                                        placeholder="600001" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $countries = App\Models\Country::get();
                                                @endphp
                                                <div class="form-group">
                                                    <label for="billing-country" class="form-label text-dark">{{ __('Country') }}</label>
                                                    <select class="form-select" name="address[country]" required>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->country_code }}" 
                                                                {{ \Auth::user()->country == $country->country_code ? 'selected' : '' }}>
                                                                {{ $country->country_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                
                                        <input type="hidden" name="plan_id" value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                        
                                        @if($paymentMode == "trial" && (!empty(\Auth::user()->plan) || !empty(\Auth::user()->plan_expire_date)))
                                            <input type="hidden" name="mode" value="{{ $paymentMode }}">
                                        @endif
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="error" style="display: none;">
                                                    <div class='alert-danger alert'>
                                                        {{ __('Please correct the errors and try again.') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="submit" value="{{ __('Confirm & Proceed') }}"
                                                class="btn btn-primary mb-2 me-3">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                    
                    
                    {{-- Cashfree --}}
                    @if ((\Auth::user()->payment_mode == "" || \Auth::user()->payment_mode == "cashfree") &&
                                    isset($admin_payment_setting['is_cashfree_enabled']) && $admin_payment_setting['is_cashfree_enabled'] == 'on')
                        <div id="cashfree_payment" class="card" style="background:#fff;">
                            <div class="card-header">
                                <h5 class="mb-3">{{ __('Cashfree') }}</h5>
                            </div>
                            <div class="tab-pane" id="cashfree_payment">
                                <form role="form" action="{{ route('plan.pay.with.cashfree') }}" method="post"
                                    id="cashfree-payment-form" class="w3-container w3-display-middle w3-card-4">
                                    @csrf

                                    {{--<div class="border p-3 mb-3 rounded">
                                        <div class="row">
                                            <div class="col-md-4 mt-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="form-group w-100">
                                                        <label for="cashfree_coupon"
                                                            class="form-label">{{ __('Coupon') }}</label>
                                                        <div class="d-flex flex-wrap coupon_withapply">
                                                            <input type="text" id="cashfree_coupon" name="coupon"
                                                                class="form-control coupon"
                                                                placeholder="{{ __('Enter Coupon Code') }}">
                                                            <a class="text-muted apply-coupon" data-bs-toggle="tooltip"
                                                                title="{{ __('Apply') }}">
                                                                Apply
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>--}}

                                    <div class="col-sm-12 my-2 px-2">
                                        <div class="text-end">
                                            <input type="hidden" name="plan_id"
                                                value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                                            <button class="btn btn-primary mb-2 me-3" type="submit" id="">
                                                {{ __('Confirm & Proceed') }}
                                            </button>

                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
