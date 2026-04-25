@extends('field_track.layouts.fieldTrack')

@section('page-title')
    {{ __('Live Location') }}
@endsection

@section('page-icon')
    {{ asset('assets/assestsnew/field_live_location.svg') }}
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/tracking/css/tracking_custom.css') }}">
@endpush

@push('script-page')

@endpush


@section('content')
    @include('field_track.layouts.partials.nav')
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12 main_border_location">
                <div class="row justify-content-between px-2 pb-3">
                    <div class="col-12 mt-2 mb-4">
                        <div class="row justify-content-between px-2 align-items-center main_location_header">
                            <div class="col-lg-4 col-12 my-3">
                                <h5>Employees <span>({{ count($employees) }})</span></h5>
                            </div>
                            <div class=" main_button_headers my-3">
                                <div class="row justify-content-lg-between">
                                    <!--<button id="btnReload" class=""><span class="badge_main col_1bage"><i class="ti ti-filter"></i></span> Reload</button>-->
                                    <button id="btnAllEmployees" class="headbtn_active"><span
                                            class="badge_main col_1bage">{{ count($employees) }}</span>All Employees
                                    </button>
                                    <button id="btnPunchedIn"><span
                                            class="badge_main col_2bage">{{ count($clock_in_employees) }}</span>Punched
                                        In
                                    </button>
                                    <button id="btnPunchedOut"><span
                                            class="badge_main col_3bage">{{ count($clock_out_employees) }}</span>Punched
                                        Out
                                    </button>
                                    <button id="btnPunchedOffline"><span
                                            class="badge_main col_4bage">{{ count($offline_employees) }}</span>Offline
                                    </button>
                                </div>
                            </div>
                            <div class="main_reloadbtn">
                                <div class="row justify-content-center">
                                    <button id="btnReload"><img
                                            src="{{ asset('assets/tracking/images/reload_arrow.svg') }}" alt=""/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12 mb-lg-0 mb-3">
                        <div class="row h-100 ps-2 pe-2 pe-lg-0">
                            <div class="col-12 main_bg_location">
                                <div class="row justify-content-center">
                                    <div class="col-lg-10 col-10 mt-3">
                                        <div class="row">
                                            <div class="col-lg-12 col-12 mt-2 mb-3">
                                                <div class="row justify-content-center padd_class">
                                                    <div class="col-12 search_employee_location">
                                                        <div class="row align-items-center py-1">
                                                            <div class="col-2 px-0">
                                                                <div
                                                                    class="row justify-content-center text-center px-0">
                                                                    <i class="ti ti-search"></i>
                                                                </div>
                                                            </div>
                                                            <div class="col-10 px-0">
                                                                <div class="row justify-content-center px-0">
                                                                    <input class="search" type="text"
                                                                           placeholder="Search Employees..."
                                                                           id="employee-search">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-10 col-11 mb-4 max_heightscrset" id="employee-list">
                                        <div
                                            class="row justify-content-center align-items-center mb-3 main_setterdash all_employees_list">
                                            @foreach($employees as $key => $emp)
                                                {{--<div class="col-2">
                                                    <div class="row justify-content-center">
                                                        <input type="checkbox" id="employee_{{ $emp->id }}" class="employee-checkbox checkbox_widthsetter">
                                                    </div>
                                                </div>
                                                <div class="col-10">
                                                    <div class="row justify-content-start">
                                                        <span class="span_employeechecker">{{ $emp->name }}</span>
                                                    </div>
                                                </div>--}}


                                                <button class="active_name" data-lat="{{ $emp->user->latitude }}"
                                                        data-lng="{{ $emp->user->longitude }}"
                                                        data-ename="{{ $emp->name }}" data-eid="{{ $emp->user->id }}">
                                                    <!--<input type="checkbox" id="employee_{{ $emp->id }}" class="employee-checkbox">-->
                                                    <span class="span_employeechecker">{{ $emp->name }}</span>
                                                </button>
                                            @endforeach
                                            <!--<button class="nonactive_btn">checker</button>-->
                                        </div>
                                        <div
                                            class="row justify-content-center align-items-center mb-3 main_setterdash clock_in_employees d-none">
                                            @foreach($clock_in_employees as $key => $emp)
                                                <button class="active_name" data-lat="{{ $emp->user->latitude }}"
                                                        data-lng="{{ $emp->user->longitude }}"
                                                        data-ename="{{ $emp->name }}" data-eid="{{ $emp->user->id }}">
                                                    <span class="span_employeechecker">{{ $emp->name }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                        <div
                                            class="row justify-content-center align-items-center mb-3 main_setterdash clock_out_employees d-none">
                                            @foreach($clock_out_employees as $key => $emp)
                                                <button class="active_name" data-lat="{{ $emp->user->latitude }}"
                                                        data-lng="{{ $emp->user->longitude }}"
                                                        data-ename="{{ $emp->name }}" data-eid="{{ $emp->user->id }}">
                                                    <span class="span_employeechecker">{{ $emp->name }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                        <div
                                            class="row justify-content-center align-items-center mb-3 main_setterdash offline_employees d-none">
                                            @foreach($offline_employees as $key => $emp)
                                                <button class="active_name" data-lat="{{ $emp->user->latitude }}"
                                                        data-lng="{{ $emp->user->longitude }}"
                                                        data-ename="{{ $emp->name }}" data-eid="{{ $emp->user->id }}">
                                                    <span class="span_employeechecker">{{ $emp->name }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-12 ps-lg-3 ps-2 pe-2">
                        <div id="map" style="height: 600px; border-radius:15px"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 my-3 main_table_location">
                <div class="row px-lg-3 ">
                    <div class="col-12 mt-5">
                        <h2>Last Location</h2>
                    </div>
                    <div class="col-12 mt-4 loction_tablescroll">
                        <table class="table">
                            <tbody class="body_tablelocation">
                            @foreach($employees as $key => $emp)
                                <tr>
                                    <td>
                                        <div class="row">
                                            <span>Name</span>
                                            <h4>{{ $emp->name }}</h4>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <span>Location</span>
                                            <p style="white-space: pre-wrap;">{{ getAddress($emp->user->latitude,$emp->user->longitude) }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <span>Location Points</span>
                                            <p>Lat: {{ $emp->user->latitude }}</p>
                                            <p>Lng: {{ $emp->user->longitude }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <span>Time</span>
                                            <p><?php echo date('h:i A', strtotime($emp->user->last_login_at)); ?></p>
                                        </div>
                                    </td>
                                    {{--<td>
                                        <div class="row">
                                        <span>Battery Level</span>
                                        @if($emp->user->battery_level != "")
                                        <p>{{ $emp->user->battery_level }} %</p>
                                        @endif
                                        </div>
                                    </td>--}}
                                    <td>
                                        <div class="row">
                                            <span>Status</span>
                                            <p>{{($emp->user->is_location == '1')?'Online':'Offline' }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="alertmodal" tabindex="-1" aria-labelledby="popupalert" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content main_alertpopus">
                <div class="row">
                    <div class="col-12">
                        <div class="row px-3 py-3 justify-content-center">
                            <div class="col-4 my-3">
                                <div class="row">
                                    <img src="{{ asset('assets/tracking/images/popupexclamation.svg') }}" alt="Image">
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="row text-center">
                                    <h4 class="message_content"></h4>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="row justify-content-center">
                                    <button type="button" class="btn planupgradebtn" id="closeModalBtn"
                                            data-dismiss="modal" data-bs-dismiss="modal">Okay
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script src="https://maps.googleapis.com/maps/api/js?key={!! env('MAP_API_KEY') !!}&callback=initMap" async
            defer></script>
    <script>

        var map;

        var employees = {!! json_encode($employees) !!};
        var markers = [];


        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: {lat: 11.0168, lng: 76.9558}
            });


            employees.forEach(function (employee) {
                if (employee.user && employee.user.latitude && employee.user.longitude) {
                    var marker = new google.maps.Marker({
                        position: {lat: parseFloat(employee.user.latitude), lng: parseFloat(employee.user.longitude)},
                        map: map,
                        title: employee.name
                    });

                    markers.push(marker);
                } else {
                    // console.error('Invalid latitude or longitude values for employee:', employee);
                }
            });
        }

        function openModal(message) {
            $('.message_content').text(message);
            $('#alertmodal').modal('show');
        }

        $(document).ready(function () {

            $('.employee-checkbox').change(function () {
                // console.log("Checkbox changed");
                // Clear all markers
                clearMarkers();

                // Iterate over all checked checkboxes
                $('.employee-checkbox:checked').each(function () {
                    var employeeId = $(this).attr('id').split('_')[1];
                    var employee = employees.find(emp => emp.id == employeeId);

                    if (employee && employee.user && employee.user.latitude && employee.user.longitude) {
                        // Add marker for the checked employee
                        var marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(employee.user.latitude),
                                lng: parseFloat(employee.user.longitude)
                            },
                            map: map,
                            title: employee.name
                        });

                        // console.log(employee.name+" "+parseFloat(employee.user.latitude)+" "+parseFloat(employee.user.longitude));

                        // Center map on the last checked employee's location
                        map.setZoom(17);
                        map.setCenter(marker.getPosition());
                        markers.push(marker); // Store the marker in the markers array
                    } else {
                        // console.error('Invalid data for employee with ID:', employeeId);
                    }
                });
            });
        });

        $(document).ready(function () {
            // Event listener for search input change
            $('#employee-search').on('input', function () {
                var searchQuery = $(this).val().toLowerCase(); // Get the search query and convert it to lowercase

                // Filter the list of employees based on the search query
                $('#employee-list .active_name').each(function () {
                    var employeeName = $(this).find('span').text().toLowerCase();
                    var checkbox = $(this).find('.employee-checkbox');
                    if (employeeName.indexOf(searchQuery) === -1) {
                        $(this).hide(); // Hide the employee if the name does not match the search query
                    } else {
                        $(this).show();
                    }
                    // checkbox.parent().css('display', 'flex');
                });
            });

            $('#btnAllEmployees').click(function () {
                initMap();
                $(this).addClass('headbtn_active'); // Add active class
                $('#btnPunchedIn').removeClass('headbtn_active');
                $('#btnPunchedOut').removeClass('headbtn_active');
                $('#btnPunchedOffline').removeClass('headbtn_active');

                $('.all_employees_list').removeClass('d-none');
                $('.clock_in_employees').addClass('d-none');
                $('.clock_out_employees').addClass('d-none');
                $('.offline_employees').addClass('d-none');
            });
            $('#btnReload').click(function () {
                if ($('.active_name.check_active').length > 0) {
                    $('.active_name.check_active').click();
                } else {
                    initMap();
                }
            });
            $('.active_name').click(function () {
                $('.active_name').removeClass('check_active');
                $(this).addClass('check_active');
                // var lati = parseFloat($(this).attr('data-lat'));
                // var lngi = parseFloat($(this).attr('data-lng'));
                var ename = $(this).attr('data-ename');
                var eid = $(this).attr('data-eid');

                $.ajax({
                    url: '{{ route("fieldTrack.getLiveLocation") }}', // Replace with your endpoint to fetch live location
                    method: 'GET',
                    data: {id: eid},
                    success: function (response) {
                        if (response && response.error) {
                            clearMarkers();
                            openModal(response.error);
                        } else {
                            // Assuming response contains latitude and longitude
                            var lati = parseFloat(response.latitude);
                            var lngi = parseFloat(response.longitude);

                            clearMarkers();
                            var marker = new google.maps.Marker({
                                position: {lat: lati, lng: lngi},
                                map: map,
                                title: ename
                            });

                            // Center map on the employee's live location
                            map.setZoom(19);
                            map.setCenter(marker.getPosition());
                            markers.push(marker);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching live location:', error);
                        // Handle error scenario
                    }
                });
            });


            $('#btnPunchedIn').click(function () {

                $(this).addClass('headbtn_active'); // Add active class
                $('#btnAllEmployees').removeClass('headbtn_active');
                $('#btnPunchedOut').removeClass('headbtn_active');
                $('#btnPunchedOffline').removeClass('headbtn_active');

                $('.all_employees_list').addClass('d-none');
                $('.clock_in_employees').removeClass('d-none');
                $('.clock_out_employees').addClass('d-none');
                $('.offline_employees').addClass('d-none');

                // Hide markers for all employees
                clearMarkers();

                // Filter punched in employees
                var punchedInEmployees = {!! json_encode($clock_in_employees) !!};

                // Show markers for punched in employees
                punchedInEmployees.forEach(function (employee) {
                    if (employee.user && employee.user.latitude && employee.user.longitude) {
                        var marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(employee.user.latitude),
                                lng: parseFloat(employee.user.longitude)
                            },
                            map: map,
                            title: employee.name
                        });

                        map.setCenter(marker.getPosition()); // Center map on the employee's location
                        markers.push(marker);
                    } else {
                        // console.error('Invalid latitude or longitude values for employee:', employee);
                    }
                });
            });


            $('#btnPunchedOut').click(function () {

                $(this).addClass('headbtn_active'); // Add active class
                $('#btnAllEmployees').removeClass('headbtn_active');
                $('#btnPunchedIn').removeClass('headbtn_active');
                $('#btnPunchedOffline').removeClass('headbtn_active');

                $('.all_employees_list').addClass('d-none');
                $('.clock_in_employees').addClass('d-none');
                $('.clock_out_employees').removeClass('d-none');
                $('.offline_employees').addClass('d-none');

                // Hide markers for all employees
                clearMarkers();

                // Filter punched in employees
                var punchedOutEmployees = {!! json_encode($clock_out_employees) !!};

                // Show markers for punched in employees
                punchedOutEmployees.forEach(function (employee) {
                    if (employee.user && employee.user.latitude && employee.user.longitude) {
                        var marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(employee.user.latitude),
                                lng: parseFloat(employee.user.longitude)
                            },
                            map: map,
                            title: employee.name
                        });

                        map.setCenter(marker.getPosition()); // Center map on the employee's location
                        markers.push(marker);
                    } else {
                        // console.error('Invalid latitude or longitude values for employee:', employee);
                    }
                });
            });

            $('#btnPunchedOffline').click(function () {

                $(this).addClass('headbtn_active'); // Add active class
                $('#btnAllEmployees').removeClass('headbtn_active');
                $('#btnPunchedIn').removeClass('headbtn_active');
                $('#btnPunchedOut').removeClass('headbtn_active');

                $('.all_employees_list').addClass('d-none');
                $('.clock_in_employees').addClass('d-none');
                $('.clock_out_employees').addClass('d-none');
                $('.offline_employees').removeClass('d-none');

                // Hide markers for all employees
                clearMarkers();

                // Filter punched in employees
                var offlineEmployees = {!! json_encode($offline_employees) !!};

                // Show markers for punched in employees
                offlineEmployees.forEach(function (employee) {
                    if (employee.user && employee.user.latitude && employee.user.longitude) {
                        var marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(employee.user.latitude),
                                lng: parseFloat(employee.user.longitude)
                            },
                            map: map,
                            title: employee.name
                        });

                        map.setCenter(marker.getPosition()); // Center map on the employee's location
                        markers.push(marker);
                    } else {
                        // console.error('Invalid latitude or longitude values for employee:', employee);
                    }
                });
            });
        });

        // Function to clear all markers from the map
        function clearMarkers() {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            markers = [];
        }

        function toggleCheckbox(empId) {
            var checkbox = $('#employee_' + empId);
            if (checkbox.length > 0) {
                var isChecked = checkbox.prop('checked');

                if (isChecked) {
                    checkbox.removeAttr('checked');
                }
                if (!isChecked) {
                    checkbox.attr('checked', true);
                }

                // checkbox.prop('checked', !isChecked);
                // console.log('Checkbox element found.');
            }
        }

    </script>
@endpush
