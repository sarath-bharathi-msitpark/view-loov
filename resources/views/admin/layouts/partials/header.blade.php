<div class="header-wrapper menu-wrapper">

    <div class="col-12 me-auto dash-mob-drp my-2">

        <!--subscription-->
        @if (
           Gate::check('manage plan') ||
           Gate::check('manage coupon') ||
           Gate::check('manage order') 
        )
            <ul class="{{ (request()->routeIs('general.plans.*') || request()->routeIs('general.coupons.*') || request()->routeIs('general.order.*') || request()->routeIs('general.plan_request.*')) ? 'd-block' : 'd-none' }} d-flex main_header_setter gap-5" style="list-style:none;">
            

                @if (Gate::check('manage plan'))
                    <li class="dash-item {{ request()->routeIs('general.plans.*') ? 'active' : '' }}">
                        <a class="dash-link" href="{{ route('general.plans.index') }}">{{ __('Plans') }}</a>
                    </li>
                @endif

                {{--@if (\Auth::user()->type == 'super admin')
                    <li class="dash-item {{ request()->routeIs('general.plan_request*') ? 'active' : '' }}">
                        <a class="dash-link" href="{{ route('general.plan_request.index') }}">
                            {{ __('Plan Request') }}
                        </a>
                    </li>
                @endif--}}

                @if (Gate::check('manage coupon'))
                    <li class="dash-item {{ request()->routeIs('general.coupons*') ? 'active' : '' }}">
                        <a class="dash-link" href="{{ route('general.coupons.index') }}">{{ __('Coupons') }}</a>
                    </li>
                @endif

                @if (Gate::check('manage order'))
                    <li class="dash-item {{ request()->routeIs('general.order*') ? 'active' : '' }}">
                        <a class="dash-link" href="{{ route('general.order.index') }}">{{ __('Orders') }}</a>
                    </li>
                @endif

            </ul>
        @endif



    </div>
</div>
