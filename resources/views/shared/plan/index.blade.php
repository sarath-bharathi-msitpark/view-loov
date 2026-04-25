@php
    $layout = auth()->user()->isSuperAdmin() ? 'admin.layouts.admin' : 'company.layouts.company';
@endphp

@extends($layout)
@php
    $dir = asset(Storage::url('uploads/plan'));
@endphp
@section('page-title')
    {{ __('Manage Plan') }}
@endsection
@section('page-icon')
    {{ asset('assets/assestsnew/subscription.svg') }}
@endsection
@push('css-page')
    <style>
        /* 20.01  */
        .price-card .list-unstyled .theme-avtar {
            width: 20px;
            margin-right: 5px !important;
        }

        .request-btn .btn {
            padding: 8px 12px !important;
        }

        @media screen and (max-width: 991px) {
            .plan_card {
                width: 50%;
            }
        }

        @media screen and (max-width: 767px) {
            .plan_card {
                width: 100%;
            }

            .plan_card .price-card {
                height: auto;
                margin-bottom: 0;
            }
        }

        @media screen and (max-width: 481px) {
            .plan_card .card-body .row .col-6 {
                width: 100%;
            }

            .plan_card .card-body .row .col-6:not(:first-of-type) .list-unstyled {
                margin: 0 0 20px !important;
            }

            .plan_card .card-body .row .col-6:first-of-type .list-unstyled {
                margin: 20px 0 7px !important;
            }

            .plan_card .price-card {
                max-height: unset;
                margin-top: 0px;
            }
        }

        /* 20.01  */
    </style>
@endpush


@section('breadcrumb')
    @if(auth()->user()->isSuperAdmin())
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ __('Plan') }}</li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('organization.dashboard') }}">{{ __('Dashboard') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ __('Plan') }}</li>
    @endif
@endsection
@section('action-btn')
    <div class="float-end">
        @can('create plan')
            {{-- @if (isset($admin_payment_setting) && !empty($admin_payment_setting)) --}}
            {{-- @if (
                $admin_payment_setting['is_manually_payment_enabled'] == 'on' ||
                    $admin_payment_setting['is_bank_transfer_enabled'] == 'on' ||
                    $admin_payment_setting['is_stripe_enabled'] == 'on' ||
                    $admin_payment_setting['is_paypal_enabled'] == 'on' ||
                    $admin_payment_setting['is_paystack_enabled'] == 'on' ||
                    $admin_payment_setting['is_flutterwave_enabled'] == 'on' ||
                    $admin_payment_setting['is_razorpay_enabled'] == 'on' ||
                    $admin_payment_setting['is_mercado_enabled'] == 'on' ||
                    $admin_payment_setting['is_paytm_enabled'] == 'on' ||
                    $admin_payment_setting['is_mollie_enabled'] == 'on' ||
                    $admin_payment_setting['is_skrill_enabled'] == 'on' ||
                    $admin_payment_setting['is_coingate_enabled'] == 'on' ||
                    $admin_payment_setting['is_paymentwall_enabled'] == 'on' ||
                    $admin_payment_setting['is_toyyibpay_enabled'] == 'on' ||
                    $admin_payment_setting['is_payfast_enabled'] == 'on' ||
                    $admin_payment_setting['is_iyzipay_enabled'] == 'on' ||
                    $admin_payment_setting['is_sspay_enabled'] == 'on' ||
                    $admin_payment_setting['is_paytab_enabled'] == 'on' ||
                    $admin_payment_setting['is_benefit_enabled'] == 'on' ||
                    $admin_payment_setting['is_cashfree_enabled'] == 'on' ||
                    $admin_payment_setting['is_aamarpay_enabled'] == 'on' ||
                    $admin_payment_setting['is_paytr_enabled'] == 'on' ||
                    $admin_payment_setting['is_yookassa_enabled'] == 'on' ||
                    $admin_payment_setting['is_midtrans_enabled'] == 'on' ||
                    $admin_payment_setting['is_xendit_enabled'] == 'on' ||
                    $admin_payment_setting['is_nepalste_enabled'] == 'on') --}}
            <a href="#" data-size="lg" data-url="{{ route('general.plans.create') }}" data-ajax-popup="true"
               data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Create New Plan') }}"
               class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
            {{-- @endif --}}
            {{-- @endif --}}
        @endcan
    </div>
@endsection


@section('content')

    @if(auth()->user()->isSuperAdmin())
        @include('admin.layouts.partials.nav')
    @else
        @include('company.layouts.partials.nav')
    @endif

    @if(\Auth::user()->type == "super admin")
        <div class="row">
            <div class="col-sm-12">
                <div class="mt-2" id="multiCollapseExample1">
                    <div class="card">
                        <div class="card-body">
                            {{ Form::open(['route' => ['general.plans.index'], 'method' => 'GET', 'id' => 'company_submit']) }}
                            <div class="row d-flex align-items-center justify-content-end gap-2">

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                                    <div class="btn-box">
                                        {{ Form::label('company_id2', __('Company'), ['class' => 'form-label']) }}
                                        {{ Form::select('company_id2', $companies, request('company_id2', ''), ['class' => 'form-control select2']) }}
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2 mt-md-0 mt-3">
                                    <div class="btn-box">
                                        {{ Form::label('search', __('Search'), ['class' => 'form-label']) }}
                                        {{ Form::text('search', request('search', ''), ['class' => 'form-control', 'placeholder' => __('Enter name, email, or contact')]) }}
                                    </div>
                                </div>

                                <div class="col-auto float-end ms-2 mt-4">
                                    <a href="#" class="btn btn-sm btn-primary me-1"
                                       onclick="document.getElementById('company_submit').submit(); return false;"
                                       data-bs-toggle="tooltip" data-bs-original-title="{{ __('Apply') }}">
                                        <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                    </a>

                                    <a href="{{ route('general.plans.index') }}" class="btn btn-sm btn-danger"
                                       data-bs-toggle="tooltip" data-bs-original-title="{{ __('Reset') }}">
                                        <span class="btn-inner--icon"><i
                                                class="ti ti-refresh text-white-off"></i></span>
                                    </a>
                                </div>

                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        @foreach ($plans as $plan)
            <div class="plan_card">
                <div class="card price-card price-1 wow animate__fadeInUp" data-wow-delay="0.2s"
                     style="
                   visibility: visible;
                   animation-delay: 0.2s;
                   animation-name: fadeInUp;
                   ">
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                        <span class="blue_badgebgs" style="width:max-content;max-width: 90%;">
                            @if(\Auth::user()->type == 'company' && $plan->plan_type != "common")
                                @if(!empty($plan->name))
                                    {{ $plan->name }}
                                @else
                                    {{ isset($plan->company) ? $plan->company->company_name : ($plan->name . ' - Common'??'No Data') }}
                                @endif
                            @else
                                {{ isset($plan->company) ? $plan->company->company_name : ($plan->name . ' - Common'??'No Data') }}
                            @endif
                            {{--
                            @if($plan->plan_type != "common")
                                {{ isset($plan->company) ? $plan->company->company_name : ($plan->name . ' - Common'??'No Data') }}
                            @else
                                {{ $plan->name }}
                            @endif
                            --}}
                        </span>
                        </div>
                        <div class="border_linesplans mt-0">
                            @if (\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id)
                                <div class="d-flex flex-row-reverse mt-2 me-3 p-0">
                                    <span class=" align-items-right">
                                        <i class="f-10 lh-1 fas fa-circle text-primary"></i>
                                        <span class="ms-2">{{ __('Active') }}</span>
                                    </span>
                                </div>
                            @endif
                            <h5 class="my-4 f-w-600 ">
                                {{ isset($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '₹' }}{{ number_format($plan->price) }}
                                <small class="text-sm">/ Per User </small>
                            </h5>

                            <h3 class="my-2 f-w-500 ">
                                Total Amount: ₹ {{ number_format($plan->getTotalPlanAmount($plan->id), 2, '.', ',') }}
                                / {{ ucfirst($plan->duration) }}
                            </h3>

                            <div class="row ">
                                <div>
                                    <ul class="list-unstyled my-4">
                                        <li class="white-sapce-nowrap"><span style="height: 20px;"
                                                                             class="theme-avtar"><i
                                                    class="text-primary ti ti-circle-plus"></i></span>{{ $plan->max_users == -1 ? __('Unlimited') : $plan->max_users }}
                                            {{ __('Users') }}</li>
                                        @if(\Auth::user()->type == 'company')
                                            @forelse(json_decode($plan->features ?? '[]') as $feature)
                                                <li>
                                                    <span class="theme-avtar"><i
                                                            class="text-primary ti ti-circle-plus"></i></span>
                                                    {{ $feature }}
                                                </li>
                                                @endforeach
                                                @endif
                                    </ul>
                                </div>
                            </div>
                            @if (\Auth::user()->type == 'super admin')
                                <div class="d-flex align-items-center justify-content-center">
                                    {{-- <a title="{{ __('Edit Plan') }}" href="#" class="btn btn-primary btn-sm btn-icon m-1 badge"
                                        data-url="{{ route('general.plans.edit', $plan->id) }}" data-ajax-popup="true"
                                        data-title="{{ __('Edit Plan') }}" data-size="lg" data-toggle="tooltip"
                                        data-original-title="{{ __('Edit') }}">
                                        <i class="ti ti-edit"></i>
                                    </a> --}}
                                    <a title="{{ __('Edit') }}" href="#" class="btn btn-info btn-sm align-items-center"
                                       data-url="{{ route('general.plans.edit', $plan->id) }}" data-ajax-popup="true"
                                       data-title="{{ __('Edit Plan') }}" data-size="lg" data-bs-toggle="tooltip"
                                       data-bs-original-title="{{ __('Edit') }}">
                                        <i class="ti ti-pencil text-white"></i>

                                    </a>
                                    @if($plan->price > 0)
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'route' => ['general.plans.destroy', $plan->id],
                                            'id' => 'delete-form-' . $plan->id,
                                        ]) !!}
                                        <a href="#!"
                                           class="bs-pass-para btn-icon mx-2 btn btn-danger btn-sm align-items-center"
                                           data-bs-toggle="tooltip"
                                           data-bs-original-title="{{ __('Delete') }}">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                        {!! Form::close() !!}
                                    @endif
                                </div>
                            @endif



                            @if (\Auth::user()->type != 'super admin')
                                <div class="request-btn">
                                    @if (
                                        $plan->price > 0 &&
                                            \Auth::user()->trial_plan == 0 &&
                                            \Auth::user()->plan != $plan->id && $plan->trial == 1)
                                        {{-- \Auth::user()->trial_expire_date > date('Y-m-d')) --}}
                                        <a href="{{ route('plan.trial', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                           class="btn btn-lg btn-primary btn-icon m-1">{{ __('Start Free Trial') }}</a>
                                    @endif
                                    @php
                                        $datetime1 = new \DateTime(\Auth::user()->plan_expire_date);
                                        $datetime2 = new \DateTime(date('Y-m-d'));
                                        $interval = $datetime2->diff($datetime1);
                                        $days     = $interval->format('%r%a');
                                        $trialDays = $plan->trial_days ?? 7;

                                        $userPlan = \App\Models\Plan::find(\Auth::user()->plan);
                                        $planGrade = "Buy";

                                        if ($userPlan) {
                                            $userPlanAmount = $userPlan->getTotalPlanAmount($userPlan->id);
                                            $planAmount = $plan->getTotalPlanAmount($plan->id);

                                            if (\Auth::user()->plan == $plan->id && $days <= 0) {
                                                $planGrade = "Renew";
                                            } elseif ($planAmount > $userPlanAmount) {
                                                $planGrade = "Upgrade";
                                            } elseif ($planAmount < $userPlanAmount) {
                                                $planGrade = "Downgrade";
                                            }
                                        }
                                    @endphp

                                    @if (\Auth::user()->type == 'company' && \Auth::user()->trial_expire_date)
                                        @if (\Auth::user()->type == 'company' && \Auth::user()->trial_plan == $plan->id)
                                            <p class="display-total-time mb-0">
                                                {{ __('Plan Trial Expired : ') }}
                                                {{ !empty(\Auth::user()->trial_expire_date) ? \Auth::user()->dateFormat(\Auth::user()->trial_expire_date) : 'lifetime' }}
                                            </p>
                                        @endif
                                    @else
                                        @if (\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id)
                                            <p class="display-total-time mb-0">
                                                {{ __('Plan Expired : ') }}
                                                {{ !empty(\Auth::user()->plan_expire_date) ? \Auth::user()->dateFormat(\Auth::user()->plan_expire_date) : 'lifetime' }}
                                            </p>
                                        @endif
                                    @endif
                                    <div class="p-2 border-0 rounded-3">
                                        <div class="text-center">
                                            @if(empty(\Auth::user()->plan) && empty(\Auth::user()->plan_expire_date))
                                                <div class="alert alert-info border rounded py-2 mb-3">
                                                    <strong>Plan Trial – {{ $trialDays }} Days</strong>
                                                    <br>
                                                    <small class="text-muted">Expires
                                                        on {{ \Auth::user()->dateFormat(date('Y-m-d', strtotime('+'.$trialDays.' days'))) }}</small>
                                                </div>
                                            @endif

                                            @if(empty(\Auth::user()->plan) && (\Auth::user()->plan != $plan->id || $days <= 0))
                                                <a href="{{ route('general.stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                                   class="btn btn-lg btn-primary btn-icon m-1 mb-2">
                                                    Buy Plan
                                                </a>
                                            @elseif(!empty(\Auth::user()->plan) && (\Auth::user()->plan != $plan->id || $days <= 0))
                                                <a href="{{ route('general.stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                                   class="btn btn-lg btn-primary btn-icon m-1 mb-2">
                                                    {{ $planGrade }}
                                                </a>
                                            @endif
                                            @if(empty(\Auth::user()->plan) && empty(\Auth::user()->plan_expire_date))
                                                @if(!empty(\Auth::user()->stripe_subscription_id) || (\Auth::user()->payment_mode == "stripe"))
                                                    <a href="{{ route('general.stripe', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                                       class="btn btn-lg btn-primary btn-icon m-1">
                                                        Start Trial
                                                    </a>
                                                @else
                                                    <a href="{{ route('general.send.request.trail', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                                       class="btn btn-lg btn-primary btn-icon m-1">
                                                        Start Trial
                                                    </a>
                                                @endif
                                            @endif


                                            @if(
                                                (\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id  && $days > 0) &&
                                                ((!empty(\Auth::user()->payment_mode) && \Auth::user()->payment_mode == 'stripe' && !empty(\Auth::user()->stripe_subscription_id)) ||
                                                (!empty(\Auth::user()->payment_mode) && \Auth::user()->payment_mode == 'cashfree'))
                                            )
                                                <a href="#"
                                                   class="btn btn-lg btn-primary btn-icon m-1 mb-2"
                                                   data-url="{{ route('general.request.license', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                                   data-title="{{ __('Buy License') }}" data-ajax-popup="true"
                                                   data-size="md" data-bs-toggle="tooltip"
                                                   data-bs-original-title="{{ __('Buy License') }}"
                                                   title="{{ __('Buy License') }}">
                                                    {{ __('Buy License') }}
                                                </a>
                                            @endif

                                        </div>
                                    </div>

                                    {{--@if ($plan->id != 1 && $plan->id != \Auth::user()->plan)
                                        @if (\Auth::user()->requested_plan != $plan->id)
                                            <a href="{{ route('general.send.request', [\Illuminate\Support\Facades\Crypt::encrypt($plan->id)]) }}"
                                                class="btn btn-lg btn-primary btn-icon m-1"
                                                data-title="{{ __('Send Request') }}" data-bs-toggle="tooltip"
                                                title="{{ __('Send Request') }}">
                                                <span class="btn-inner--icon"><i class="ti ti-corner-up-right"></i></span>
                                            </a>
                                        @else
                                            <a href="{{ route('general.request.cancel', \Auth::user()->id) }}"
                                                class="btn btn-lg btn-danger btn-icon m-1"
                                                data-title="{{ __('`Cancle Request') }}" data-bs-toggle="tooltip"
                                                title="{{ __('Cancle Request') }}">
                                                <span class="btn-inner--icon"><i class="ti ti-x"></i></span>
                                            </a>
                                        @endif
                                    @endif--}}
                                </div>
                            @endif


                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('script-page')
    <script>
        $(document).on('change', '#trial', function () {
            if ($(this).is(':checked')) {
                $('.plan_div').removeClass('d-none');
                $('#trial_days').attr("required", true);

            } else {
                $('.plan_div').addClass('d-none');
                $('#trial_days').removeAttr("required");
            }
        });
    </script>

    <script>
        $(document).on("click", ".is_disable", function () {

            var id = $(this).attr('data-id');
            var is_disable = ($(this).is(':checked')) ? $(this).val() : 0;

            $.ajax({
                url: '{{ route('plan.disable') }}',
                type: 'POST',
                data: {
                    "is_disable": is_disable,
                    "id": id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
                    if (data.success) {
                        show_toastr('success', data.success);
                    } else {
                        show_toastr('error', data.error);

                    }

                }
            });
        });
    </script>

@endpush
