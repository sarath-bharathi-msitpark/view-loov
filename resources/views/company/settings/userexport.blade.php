<table>
    <thead>
    <tr>
        <th>Employee No</th>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Gender</th>
        <th>Date of Birth</th>
        <th>Date of Join</th>
        <th>Team</th>
        <th>Role</th>
        <th>Designation</th>
        <th>Shift</th>

    </tr>
    </thead>
    <tbody>
    @forelse ($employees as $emp)
        <tr>
            <td>{{ $emp->employee_id ?? '' }}</td>
            <td>{{ $emp->name ?? '' }}</td>
            <td>{{ $emp->email ?? '' }}</td>
            <td>{{ $emp->mobile_no ?? '' }}</td>
            <td>{{ $emp->gender ?? '' }}</td>
            <td>{{ $emp->dob ?? '' }}</td>
            <td>{{ $emp->company_doj ?? '' }}</td>
            <td>{{ $emp->team ? $emp->team->name : '-' }}</td>
            <td>{{ $emp->role ? $emp->role->name : '-' }}</td>
            <td>{{ $emp->designation ? $emp->designation->name : '-' }}</td>
            <td>{{ $emp->shift ? $emp->shift->shift_name : '-' }}</td>
        </tr>

    @empty
        <tr>
            <td>No Record Found</td>
        </tr>
    @endforelse
    </tbody>
</table>
