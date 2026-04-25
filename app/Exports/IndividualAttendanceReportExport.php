<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IndividualAttendanceReportExport implements FromCollection, WithHeadings
{
    protected $attendances;

    public function __construct($attendances)
    {
        $this->attendances = $attendances;
    }

    public function collection()
    {
        if ($this->attendances->isEmpty()) {
            return collect([
                ['No records found', '', '', '', '', '', '', '', '', '', '']
            ]);
        }

        return $this->attendances->map(function ($attendance) {
            return [
                'Employee Name' => $attendance->employee->user->name ?? '',
                'Employee Id' => $attendance->employee->employee_id,
                'Team' => $attendance->employee->team->name ?? '',
                'Shift' => $attendance->employee->shift->shift_name ?? '',
                'Date' => $attendance->date,
                'Clock In' => $attendance->clock_in,
                'Clock Out' => $attendance->clock_out,
                'Late' => $attendance->late,
                'Early Leaving' => $attendance->early_leaving,
                'Overtime' => $attendance->overtime,
                'Total Rest' => $attendance->total_rest,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Employee Id',
            'Team',
            'Shift',
            'Date',
            'Clock In',
            'Clock Out',
            'Late',
            'Early Leaving',
            'Overtime',
            'Total Rest',
        ];
    }
}
