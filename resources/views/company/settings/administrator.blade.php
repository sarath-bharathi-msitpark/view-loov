@extends('company.layouts.company')
@section('page-title')
    {{ __('Administrator Settings') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/settings.svg') }}
@endsection

@push('css-page')
    <style>
        .disabled-btn {
            pointer-events: auto;
            opacity: 0.6;
        }
    </style>
@endpush

@push('theme-script')
@endpush

@push('script-page')
    <script>
        $(document).ready(function () {
            $('#submitaddadmin').on('click', function (e) {
                e.preventDefault();

                let form = $('#adduservalidate');
                let url = form.attr('action');
                let formData = form.serialize();

                // Clear all previous errors
                $('[id^=adderror-]').text('');

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    success: function (response) {
                        $('#AddTeamModal').modal('hide');

                        location.reload();
                        show_toastr('Success', 'User created successfully.', 'success');

                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $('#adderror-' + key).text(value[0]);
                            });
                        } else {
                            show_toastr('Error', 'Something went wrong. Please try again.', 'error');
                        }
                    }
                });
            });
        });
    </script>

    <script>
        $('#editAdminModal').on('shown.bs.modal', function (event) {
            var button = $(event.relatedTarget);

            var id = button.data('id');
            var name = button.data('name');
            var email = button.data('email');
            var mobile_no = button.data('mobile_no');
            var dob = button.data('dob');
            var date_of_join = button.data('date_of_join');
            var gender = button.data('gender');
            var role_id = button.data('role_id');
            var employee_id = button.data('employee_id');
            var is_active = button.data('is_active');

            var modal = $(this);
            var form = modal.find('#editAdminForm');

            let baseUrl = "{{ route('organization.setting.administrator.update', ['id' => '::ID::']) }}".replace('::ID::', id);
            form.attr('action', baseUrl);

            form.find('#employee_id').val(id);
            form.find('#editid').val(id);
            form.find('#teamName').val(name);
            form.find('#teamEmail').val(email);
            form.find('#mobile_no').val(mobile_no);
            form.find('#dob').val(dob);
            form.find('#date_of_join').val(date_of_join);
            form.find('#employeeId').val(employee_id);
            form.find('#editpassword').val('');

            modal.find('.select2').select2({
                dropdownParent: modal,
                width: '100%'
            });

            form.find('#gender').val(gender).trigger('change');
            form.find('#role_id').val(role_id).trigger('change');
            form.find('#editis_active').val(is_active.toString()).trigger('change');
        });
    </script>

    <script>
        $('#submitEditUser').click(function (e) {
            e.preventDefault();

            let form = $('#editAdminForm')[0];
            let formData = new FormData(form);

            formData.append('_method', 'PUT');

            let url = $('#editAdminForm').attr('action');

            $('#editAdminForm .text-danger').text('');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#editAdminModal').modal('hide');
                    show_toastr('Success', 'User updated successfully.', 'success');
                    location.reload();
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            $('#editerror-' + key).text(value[0]);
                        });
                    } else {
                        show_toastr('Error', 'Something went wrong. Please try again.', 'error');
                    }
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('.toggle-active').change(function () {
                const checkbox = $(this);
                const url = checkbox.data('url');
                const isActive = checkbox.is(':checked') ? 1 : 0;

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        is_active: isActive
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Status updated successfully',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update status',
                            confirmButtonText: 'OK'
                        });
                        checkbox.prop('checked', !isActive);
                    }
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('.adduser_btn').addEventListener('click', function (e) {
                // Only trigger warning if button has 'disabled-btn' class
                if (this.classList.contains('disabled-btn')) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'User Limit Reached',
                        text: 'You have reached the maximum number of users allowed.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#teamSelect').on('change', function () {
                $(this).closest('form').submit();
            });
        });
    </script>
@endpush

@section('content')
    @include('company.layouts.partials.nav')

    @php
        use Illuminate\Support\Str;
    @endphp

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="col-12 entire_box1 mb-5">
        @include('company.settings.tab')

        <div class="row mt-5">
            <div class="col-12 col-md-6 col-lg-6  d-flex align-items-center gap-3">
                <div class="col-12 col-lg-12">
                    <div class="d-flex search_maincontain" style="max-width: 400px;">
                        <form method="GET" action="{{ route('organization.setting.administrator.index') }}"
                              class="form_settings d-flex w-100">
                            <input type="text" placeholder="Search" name="search" value="{{ request('search') }}">
                            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mt-2 mt-md-0 col-12 col-md-6 col-lg-6 d-flex  justify-content-end">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <button
                        onclick="window.location.href='{{ route('organization.setting.administrator.download', request()->all()) }}'"
                        class="download_arrbtn">
                        <i class="fas fa-download"></i>
                    </button>

                    <button
                        type="button"
                        class="d-flex align-items-center gap-1 adduser_btn"
                        data-bs-toggle="modal"
                        data-bs-target="#AddAdminModal">
                        <img src="{{ asset('assets/assestsnew/plus.svg') }}" alt="">
                        <span class="text-white">Add Administrator</span>
                    </button>
                </div>
            </div>
        </div>

        @php $i = ($employees->currentPage() - 1) * $employees->perPage() + 1; @endphp

        {{--Add Admin Model--}}
        <div class="modal fade" id="AddAdminModal" tabindex="-1" aria-labelledby="AddAdminModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:800px">
                <div class="modal-content rounded-5 shadow p-4 w-100">
                    <div class="modal-header border-0">
                        <h5 class="modal-title w-100 text-center fw-semibold fs-4" id="AddAdminModalLabel">Add
                            Administrator</h5>
                    </div>
                    <div class="modal-body w-100">
                        <form method="POST" action="{{ route('organization.setting.administrator.store') }}"
                              id="adduservalidate">

                            @csrf
                            <div class="row mt-3 g-3">
                                <div class="col-lg-6 col-md-6 col-12">
                                    <label for="teamName" class="form-label fw-semibold">Name<span
                                            style="color: red;"></span></label>
                                    <input type="text" class="form-control" style="border-radius: 100px;"
                                           id="basic_name"
                                           name="name" placeholder="Enter Name" required>
                                    <div class="text-danger" id="adderror-name"></div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-12">
                                    <label for="" class="form-label fw-semibold">Employee ID<span
                                            style="color: red;">*</span></label>
                                    <input type="text" class="form-control" name="employee_id"
                                           style="border-radius: 100px;" id="basic_empid" placeholder="Enter ID"
                                           required>
                                    <div class="text-danger" id="adderror-employee_id"></div>
                                </div>


                                <div class="col-lg-6 col-md-6 col-12">
                                    <label for="teamEmail" class="form-label fw-semibold">Email<span
                                            style="color: red;">*</span></label>
                                    <input type="email" class="form-control" style="border-radius: 100px;"
                                           id="basic_email" name="email" placeholder="Enter Email" required>
                                    <div class="text-danger" id="adderror-email"></div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-12">
                                    <label for="password" class="form-label fw-semibold">Password<span
                                            style="color: red;">*</span></label>
                                    <input type="text" class="form-control" name="password" id="basic_password"
                                           placeholder="Enter password" style="border-radius: 100px;" required
                                           minlength="6">
                                    <div class="text-danger" id="adderror-password"></div>
                                </div>


                                <div class="col-lg-6 col-md-6 col-12">
                                    <label for="" class="form-label fw-semibold">Gender <span
                                            style="color: red;">*</span></label>
                                    <select class="form-select select2" id="basic_gender" required name="gender"
                                            style="border-radius: 100px;max-width:none !important;">
                                        <option selected disabled>Select Employee Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Others</option>
                                    </select>
                                    <div class="text-danger" id="adderror-gender"></div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-12">
                                    <label for="" class="form-label fw-semibold">Date of Birth</label>
                                    <input type="date" class="form-control dob-input" id="basic_dob" name="dob"
                                           style="border-radius: 100px;" required>
                                    <div class="text-danger" id="adderror-dob"></div>
                                </div>


                                <div class="col-lg-6 col-md-6 col-12">
                                    <label for="" class="form-label fw-semibold">Date of Joining<span
                                            style="color: red;">*</span></label>
                                    <input type="date" class="form-control dob-input" id="basic_doj"
                                           style="border-radius: 100px;" name="date_of_join"
                                           required>
                                    <div class="text-danger" id="adderror-date_of_join"></div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-12">
                                    <label for="" class="form-label fw-semibold">Phone Number<span
                                            style="color: red;">*</span></label>
                                    <input type="tel" class="form-control dob-input" placeholder="Enter Number"
                                           id="basic_phone" required name="mobile_no" style="border-radius: 100px;">
                                    <div class="text-danger" id="adderror-mobile_no"></div>
                                </div>


                                <div class="col-lg-6 col-md-6 col-12">
                                    <label for="role_id" class="form-label fw-semibold">Role <span
                                            style="color: red;">*</span></label>
                                    <select name="role_id" class="form-select select2" id="basic_roleid"
                                            style="border-radius: 100px; max-width:none !important;"
                                            required>
                                        <option disabled selected>Select Employee Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ Str::title($role->name) }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger" id="adderror-role_id"></div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-12">
                                    <label for="is_active" class="form-label fw-semibold">Status<span
                                            style="color: red;">*</span></label><br>
                                    <select name="is_active" class="form-select select2" id="is_active" required
                                            style="border-radius: 100px; max-width:none !important;">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                    <div class="text-danger" id="adderror-is_active"></div>
                                </div>
                            </div>

                            <div
                                class="d-flex justify-content-lg-end justify-content-md-end justify-content-center gap-2 mt-4">
                                <button type="button" class="btn btn-outline-primary px-4" data-bs-dismiss="modal"
                                        style="border-radius: 100px;">Cancel
                                </button>
                                <button type="button" id="submitaddadmin" class="btn btn-primary px-4"
                                        style="border-radius: 100px;">Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-12 bg-white rounded-3">
                <div class="">
                    <div class="attendance-table-outer1">
                        <table class="attendance-table">
                            <thead>
                            <tr>
                                <th style="background-color: #F4F4F4;">
                                    <div class="d-flex align-items-center">
                                        {{-- <input class="form-check-input me-2" type="checkbox" id="serialcheckbox">--}}
                                        <span>SNo</span>
                                    </div>
                                </th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Emp ID</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td class="tex_fix">
                                        <div class="d-flex align-items-center">
                                            {{-- <input class="form-check-input me-2 employee-checkbox" type="checkbox"--}}
                                            {{-- data-id="{{ $employee->id }}">--}}
                                            <span>{{ $i++ }}</span>
                                        </div>
                                    </td>
                                    <td class="tex_fix">{{ $employee->name }}</td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->employee_id ?? '-' }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" style="width: 40px; height: 20px; color: #fff;"
                                                   class="form-check-input toggle-active border-0"
                                                   data-url="{{ route('organization.setting.administrator.toggle_active', $employee->id) }}"
                                                {{ $employee->emp_is_active == 1 ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-start align-items-center gap-2">
                                            <button class="copy_com" data-bs-toggle="modal"
                                                    data-bs-target="#editAdminModal" data-id="{{ $employee->id }}"
                                                    data-name="{{ $employee->name }}"
                                                    data-email="{{ $employee->email }}"
                                                    data-mobile_no="{{ $employee->mobile_no }}"
                                                    data-dob="{{ $employee->dob }}"
                                                    data-date_of_join="{{ $employee->company_doj }}"
                                                    data-gender="{{ $employee->gender }}"
                                                    data-role_id="{{ $employee->emp_role_id }}"
                                                    data-designation_id="{{ $employee->emp_designation_id }}"
                                                    data-shift_id="{{ $employee->shift_id }}"
                                                    data-team_id="{{ $employee->emp_team_id }}"
                                                    data-employee_id="{{ $employee->employee_id }}"
                                                    data-is_active="{{ $employee->is_active }}">
                                                <i class="fas fa-edit text-primary fs-6" title="Edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="row justify-content-center text-center">
                                            <img class="w-25"
                                                 src="{{ asset('assets/assestsnew/no_datasvg.svg') }}"
                                                 alt="">
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination Links -->
                <div class="row mt-3 justify-content-center align-items-center">
                    <div class="col-12 optional_inputpagi">
                        <div class="row align-items-start justify-content-center">

                            <!-- Left Spacer -->
                            <div class="col-12 col-md-3 mb-3 mb-md-0">
                                <div class="data_table_select">
                                    <form id="perPageForm" method="GET" action="{{ url()->current() }}"
                                          class="d-flex align-items-center gap-2 m-0">
                                        {{-- Preserve filters --}}
                                        @foreach(request()->except(['page', 'per_page']) as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endforeach

                                        <label for="per_page" class="small mb-0 text-nowrap">Items per page:</label>

                                        <select name="per_page" id="per_page"
                                                class="form-select form-select-sm"
                                                style="width: 90px; min-width: 80px;"
                                                onchange="document.getElementById('perPageForm').submit()">
                                            @foreach([5, 10, 20, 50] as $size)
                                                <option
                                                    value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                                    {{ $size }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                            </div>

                            <!-- Pagination Numbers -->
                            <div class="col-12 col-md-auto mb-3 mb-md-0 optional_inputpagi">
                                <div class="d-flex justify-content-center">
                                    <ul class="paginatio_ulist d-flex align-items-center gap-lg-4 gap-2 m-0 p-0">
                                        {{-- First Page --}}
                                        <li class="{{$employees->onFirstPage() ? 'disabled' : '' }}">
                                            <a href="{{$employees->url(1) }}" class="page-link1">&#171;</a>
                                        </li>

                                        {{-- Previous Page --}}
                                        <li class="{{$employees->onFirstPage() ? 'disabled' : '' }}">
                                            <a href="{{$employees->previousPageUrl() }}" class="page-link1">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </a>
                                        </li>

                                        {{-- Page Numbers --}}
                                        @php
                                            $start = max(1,$employees->currentPage() - 2);
                                            $end = min($start + 4,$employees->lastPage());
                                        @endphp
                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="{{$employees->currentPage() == $i ? 'active_pagination' : '' }}">
                                                <a href="{{$employees->url($i) }}"
                                                   class="page-link1">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        {{-- Next Page --}}
                                        <li class="{{ !$employees->hasMorePages() ? 'disabled' : '' }}">
                                            <a href="{{$employees->nextPageUrl() }}" class="page-link1">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </a>
                                        </li>

                                        {{-- Last Page --}}
                                        <li class="{{ !$employees->hasMorePages() ? 'disabled' : '' }}">
                                            <a href="{{$employees->url($employees->lastPage()) }}"
                                               class="page-link1">&#187;</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Page Jump Input -->
                            <div class="col-12 col-lg-3 col-md-4 mb-3 mb-md-0">
                                <div
                                    class="d-flex flex-md-row align-items-center justify-content-center justify-content-md-start gap-2">
                                    <form action="{{ url()->current() }}" method="GET"
                                          class="d-flex flex-wrap align-items-center gap-2"
                                          style="flex-direction:row !important;">
                                        {{-- Preserve filters --}}
                                        @foreach(request()->except('page') as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endforeach

                                        <input type="number" name="page" min="1" max="{{ $employees->lastPage() }}"
                                               class="form-control form-control-sm" style="width: 80px;"
                                               placeholder="Page">

                                        <button class="btn btn-sm btn-primary" type="submit">Go</button>
                                    </form>
                                    <span class="text-nowrap small text-center text-md-start">of {{ $employees->total() }} Data</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-center">
                                    <span>{{$employees->firstItem() }} to {{$employees->lastItem() }} of {{$employees->total() }} Data</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{--Edit Admin Model--}}
    <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:800px">
            <div class="modal-content rounded-5 shadow p-4 w-100">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100 text-center fw-semibold fs-4" id="editAdminModalLabel">Edit
                        Administrator</h5>
                </div>
                <div class="modal-body w-100">
                    <form method="POST" id="editAdminForm" action="">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="employee_id">
                        <input type="hidden" id="editid" name="id">

                        <!-- Form Fields -->
                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="teamName" class="form-label fw-semibold">Name<span
                                        style="color: red;">*</span></label>
                                <input type="text" class="form-control" id="teamName" name="name" required>

                                <div class="text-danger" id="editerror-name"></div>
                            </div>

                            <div class="col-6">
                                <label for="" class="form-label fw-semibold">Employee ID</label>
                                <input type="text" class="form-control" name="employee_id"
                                       style="border-radius: 100px;" id="employeeId" placeholder="Enter ID" required>
                                <div class="text-danger" id="editerror-employee_id"></div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="teamEmail" class="form-label fw-semibold">Email<span
                                        style="color: red;">*</span></label>
                                <input type="email" class="form-control" id="teamEmail" name="email" required>
                                <div class="text-danger" id="editerror-email"></div>
                            </div>

                            <div class="col-6">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <input type="text" class="form-control" name="password" id="editpassword"
                                       style="border-radius: 100px;" minlength="6">
                                <div class="text-danger" id="editerror-password"></div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="gender" class="form-label fw-semibold">Gender <span
                                        style="color: red;">*</span></label>
                                <select class="form-select select2" id="gender" required name="gender"
                                        style="border-radius: 100px;max-width:none !important;">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Others</option>
                                </select>
                                <div class="text-danger" id="editerror-gender"></div>
                            </div>

                            <div class="col-6">
                                <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" required>

                                <div class="text-danger" id="editerror-dob"></div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="date_of_join" class="form-label fw-semibold">Date of Joining<span
                                        style="color: red;">*</span></label>
                                <input type="date" class="form-control" id="date_of_join" name="date_of_join"
                                       required>
                                <div class="text-danger" id="editerror-date_of_join"></div>
                            </div>

                            <div class="col-6">
                                <label for="mobile_no" class="form-label fw-semibold">Phone Number<span
                                        style="color: red;">*</span></label>
                                <input type="tel" class="form-control" id="mobile_no" name="mobile_no" required>
                                <div class="text-danger" id="editerror-mobile_no"></div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <label for="role_id" class="form-label fw-semibold">Role <span
                                        style="color: red;">*</span></label>
                                <select name="role_id" class="form-select select2" id="role_id"
                                        style="border-radius: 100px; max-width:none !important;"
                                        required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ Str::title($role->name) }}</option>
                                    @endforeach
                                </select>

                                <div class="text-danger" id="editerror-role_id"></div>
                            </div>

                            <div class="col-6">
                                <label for="is_active" class="form-label fw-semibold">Status</label><br>
                                <select id="editis_active" name="is_active" class="form-select select2"
                                        style="border-radius: 100px;max-width:none !important;">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>

                                <div class="text-danger" id="editerror-is_active"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-outline-primary px-4" data-bs-dismiss="modal"
                                    style="border-radius: 100px;">Cancel
                            </button>
                            <button type="button" class="btn btn-primary px-4" id="submitEditUser"
                                    style="border-radius: 100px;">Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
