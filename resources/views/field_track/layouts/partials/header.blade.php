<div class="header-wrapper menu-wrapper">

    <div class="col-12 me-auto dash-mob-drp my-2">

        <!--subscription-->
        @if (
           Gate::check('manage plan') ||
           Gate::check('manage order')
        )
            <ul class="{{ (request()->routeIs('general.plans.*') || request()->routeIs('general.order.*')) ? 'd-block' : 'd-none' }} d-flex main_header_setter"
                style="list-style:none;">

                @if (Gate::check('manage plan'))
                    <li class="dash-item {{ request()->routeIs('general.plans.*') ? 'active' : '' }}">
                        <a class="dash-link" href="{{ route('general.plans.index') }}">{{ __('Plans') }}</a>
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
