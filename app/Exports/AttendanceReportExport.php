<?php

namespace App\Exports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceReportExport implements FromCollection, WithHeadings
{
    protected $reports;

    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    public function collection()
    {
        if ($this->reports->isEmpty()) {
            return collect([
                [
                    'Employee Name' => 'No records found',
                    'Employee Id' => '',
                    'Team' => '',
                    'Shift' => '',
                    'Working Days' => '',
                    'Present Days' => '',
                    'Absent Days' => '',
                    'Online Hours' => '',
                    'Active Hours' => '',
                    'Break hours' => '',
                    'Over Time' => ''
                ]
            ]);
        }

        return $this->reports->map(function ($report) {
            return [
                'Employee Name' => $report['employee']->user->name ?? '',
                'Employee Id' => $report['employee']->employee_id ?? '',
                'Team' => $report['employee']->team->name ?? '',
                'Shift' => $report['employee']->shift->shift_name ?? '',
                'Working Days' => $report['working_days'] ?? 0,
                'Present Days' => $report['present_days'] ?? 0,
                'Absent Days' => $report['absent_days'] ?? 0,
                'Online Hours' => $report['online_hours'] ?? '00:00:00',
                'Active Hours' => $report['active_hours'] ?? '00:00:00',
                'Break hours' => $report['break_hours'] ?? '00:00:00',
                'Over Time' => $report['overtime'] ?? '00:00:00',
            ];
        });
    }

    /**
     * @return string[]
     */
    public function headings(): array
    {
        return [
            'Employee Name',
            'Employee Id',
            'Team',
            'Shift',
            'Working Days',
            'Present Days',
            'Absent Days',
            'Online Hours',
            'Active Hours',
            'Break hours',
            'Over Time',
        ];
    }
}
