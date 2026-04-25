{{ Form::model($user, ['route' => ['admin.users.update', $user->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group ">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                <x-required></x-required>
                {{ Form::text('name', null, ['class' => 'form-control font-style', 'placeholder' => __('Enter User Name'), 'required' => 'required']) }}
                @error('name')
                <small class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
                <x-required></x-required>
                {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter User Email'), 'required' => 'required']) }}
                @error('email')
                <small class="invalid-email" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        </div>

        {{-- ── Status Toggle ──────────────────────────────────────────────────── --}}
        <div class="col-md-12 mb-3">
            <div class="form-group d-flex align-items-center gap-3">
                {{ Form::label('is_active', __('Status'), ['class' => 'form-label mb-0']) }}
                <div class="form-check form-switch mb-0">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        role="switch"
                        id="is_active_toggle"
                        name="is_active"
                        value="1"
                        {{ $user->is_active == 1 ? 'checked' : '' }}
                        data-current="{{ $user->is_active }}"
                        data-user-id="{{ $user->id }}"
                    >
                    <label class="form-check-label fw-semibold" id="status_label" for="is_active_toggle">
                        {{ $user->is_active == 1 ? __('Active') : __('Inactive') }}
                    </label>
                </div>
            </div>
        </div>
        {{-- ── /Status Toggle ─────────────────────────────────────────────────── --}}

        @if (\Auth::user()->type == 'super admin')

            <div class="form-group mb-3">
                <label for="company_name" class="form-label">{{ __('Company Name') }}</label>
                {{ Form::text('company_name', null, ['class' => 'form-control', 'placeholder' => __('Enter company name'), 'required' => 'required']) }}

                @error('company_name')
                <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="domain" class="form-label">{{ __('Domain') }}</label>
                {{ Form::url('domain', null, ['class' => 'form-control', 'placeholder' => __('Enter domain'), 'required' => 'required']) }}

                @error('domain')
                <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="country" class="form-label">Country</label>

                    @php
                        $country = App\Models\Country::where('created_by',1)->get();
                    @endphp
                    <select id="country" name="country" class="form-control" required="required">
                        <option value="">Select a country</option>
                        @foreach ($country as $con)
                            <option value="{{ $con->code }}"
                                {{ $user->country == $con->code ? 'selected' : '' }}>{{ $con->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('country')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="mobile_no" class="form-label">{{ __('Mobile No') }}</label>
                    <div class="input-group">
                        @php
                            $countries = App\Models\Country::where('created_by',1)->get();
                        @endphp

                            <!-- Country code dropdown -->
                        <select class="form-control" name="country_code" required>
                            @foreach ($countries as $country)
                                <option value="{{ $country->code }}"
                                    {{ $user->country_code == $country->code ? 'selected' : '' }}>
                                    +{{ $country->code }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Mobile number input -->
                        <input id="mobile_no" type="tel" class="form-control" name="mobile_no"
                               value="{{ old('mobile_no', $user->mobile_no ?? '') }}"
                               autocomplete="tel"
                               placeholder="{{ __('Enter mobile number') }}" required>

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label">Preferred Payment Mode</label>
                    @if ($admin_payment_setting['is_bank_transfer_enabled'] == 'on' && !empty($admin_payment_setting['bank_details']))
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_mode" id="bank_transfer"
                                   value="bank_transfer" @if($user->payment_mode == "bank_transfer") checked @endif >
                            <label class="form-check-label" for="bank_transfer">
                                {{ __('Bank Transfer') }}
                            </label>
                        </div>
                    @endif
                    @if ($admin_payment_setting['is_stripe_enabled'] == 'on' && !empty($admin_payment_setting['stripe_key']) && !empty($admin_payment_setting['stripe_secret']))
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_mode" id="stripe" value="stripe"
                                   @if($user->payment_mode == "stripe") checked @endif >
                            <label class="form-check-label" for="stripe">
                                {{ __('Stripe') }}
                            </label>
                        </div>
                    @endif
                    @if ($admin_payment_setting['is_cashfree_enabled'] == 'on' && !empty($admin_payment_setting['cashfree_api_key']) && !empty($admin_payment_setting['cashfree_secret_key']))
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_mode" id="cashfree"
                                   value="cashfree" @if($user->payment_mode == "cashfree") checked @endif >
                            <label class="form-check-label" for="cashfree">
                                {{ __('Cashfree') }}
                            </label>
                        </div>
                    @endif
                </div>
            </div>


            <div class="form-group">

                <div class="col-md-12">
                    <div class="form-group">
                        @if(!empty($permissions))
                            <h6 class="my-3">{{ __('Assign Menu Permission') }}</h6>
                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox"
                                               class="form-check-input align-middle custom_align_middle"
                                               name="staff_checkall"
                                               id="staff_checkall">
                                    </th>
                                    <th>{{ __('Module') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($modules as $module)
                                    @if(in_array($module, (array) $permissions))
                                        @php
                                            $key = array_search($module, $permissions);
                                        @endphp
                                        @if($key !== false)
                                            <tr>
                                                <td>
                                                    {{ Form::checkbox(
                                                        'permissions[]',
                                                        $key,
                                                        $user->can($module),
                                                        [
                                                            'class' => 'form-check-input align-middle ischeck staff_checkall',
                                                            'id' => 'permission' . $key,
                                                            'data-id' => str_replace([' ', '&'], '', $module),
                                                        ]
                                                    ) }}
                                                </td>
                                                <td>
                                                    <label
                                                        for="permission{{ $key }}"
                                                        class="ischeck staff_checkall"
                                                        data-id="{{ str_replace([' ', '&', '_'], '', $module) }}"
                                                    >
                                                        {{ ucwords(str_replace([' ', '&', '_'], ' ', $module)) }}
                                                    </label>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>

        @endif

        <!--//user start edit-->

        <div class="row">
            @php
                $countries = App\Models\Country::get();
            @endphp
        </div>

        <!--user end-->

        @if (\Auth::user()->type != 'super admin')
            <div class="form-group col-md-12">
                {{ Form::label('role', __('User Role'), ['class' => 'form-label']) }}
                <x-required></x-required>
                {!! Form::select('role', $roles, $user->roles, ['class' => 'form-control select', 'required' => 'required']) !!}
                @error('role')
                <small class="invalid-role" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </small>
                @enderror
            </div>
        @endif
        @if (!$customFields->isEmpty())
            @include('customFields.formBuilder')
        @endif
    </div>

</div>

<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>

{{ Form::close() }}

<script>
    $(document).ready(function () {

        // ── Permissions checkall ──────────────────────────────────────────────
        $("#staff_checkall").click(function () {
            $('.staff_checkall').not(this).prop('checked', this.checked);
        });
        $(".ischeck").click(function () {
            var ischeck = $(this).data('id');
            $('.isscheck_' + ischeck).prop('checked', this.checked);
        });

        // ── Status toggle ─────────────────────────────────────────────────────
        $('#is_active_toggle').on('change', function () {
            const isChecked = $(this).is(':checked');   // true = want Active
            const wasActive = $(this).data('current') == 1;
            const $toggle = $(this);
            const $label = $('#status_label');

            // ── CASE 1: Active → Inactive ─────────────────────────────────────
            if (wasActive && !isChecked) {
                Swal.fire({
                    title: '<span style="color:#e3342f;">Deactivate Company?</span>',
                    html: `
                        <p class="text-muted mb-2">
                            Setting this company to <strong>Inactive</strong> will:
                        </p>
                        <ul class="text-start text-muted" style="font-size:0.9rem;">
                            <li>Disable login for this company account</li>
                            <li>Set <strong>all employees</strong> of this company to <strong>Inactive</strong></li>
                        </ul>
                        <p class="text-muted mt-2" style="font-size:0.9rem;">Do you want to continue?</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Deactivate',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Keep toggle unchecked, update label
                        $label.text('Inactive').removeClass('text-success').addClass('text-danger');
                        $toggle.data('current', 0);
                    } else {
                        // Revert toggle back to checked (Active)
                        $toggle.prop('checked', true);
                        $label.text('Active').removeClass('text-danger').addClass('text-success');
                    }
                });

                // ── CASE 2: Inactive → Active ─────────────────────────────────────
            } else if (!wasActive && isChecked) {
                Swal.fire({
                    title: '<span style="color:#f59e0b;">Activate Company?</span>',
                    html: `
                        <div class="alert alert-warning text-start" style="font-size:0.9rem;">
                            <i class="ti ti-alert-triangle me-1"></i>
                            <strong>Note:</strong> The employees under this company are currently
                            <strong>inactive</strong>. Please remember to activate the individual
                            users after enabling this company.
                        </div>
                        <p class="text-muted" style="font-size:0.9rem;">Do you want to activate this company?</p>
                    `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Activate',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Keep toggle checked, update label
                        $label.text('Active').removeClass('text-danger').addClass('text-success');
                        $toggle.data('current', 1);
                    } else {
                        // Revert toggle back to unchecked (Inactive)
                        $toggle.prop('checked', false);
                        $label.text('Inactive').removeClass('text-success').addClass('text-danger');
                    }
                });

                // ── No change (safety fallback) ───────────────────────────────────
            } else {
                $label.text(isChecked ? 'Active' : 'Inactive');
            }
        });

        // Set initial label colour on page load
        (function () {
            const $toggle = $('#is_active_toggle');
            const $label = $('#status_label');
            if ($toggle.is(':checked')) {
                $label.addClass('text-success');
            } else {
                $label.addClass('text-danger');
            }
        })();
    });
</script>
