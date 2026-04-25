<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $attendanceData;

    public function __construct($attendanceData)
    {
        // Ensure it's always a collection
        $this->attendanceData = collect($attendanceData);
    }

    public function collection()
    {
        if ($this->attendanceData->isEmpty()) {
            return collect([
                [
                    'Employee'           => 'No records found',
                    'Date'               => '',
                    'Punched In'         => '',
                    'Punched Out'        => '',
                    'Total KM Travelled' => '',
                    'Status'             => '',
                ]
            ]);
        }

        return $this->attendanceData->map(function ($attendance) {
            return [
                'Employee' => $attendance->employee->name ?? '-',
                'Date' => auth()->user()->dateFormat($attendance->date),
                'Punched In' => ($attendance->clock_in != '00:00:00')
                    ? auth()->user()->timeFormat($attendance->clock_in)
                        . "\nODO: " . ($attendance->start_ride ?? '-')
                        . "\nLocation: " . ($attendance->getAddress($attendance->clock_in_latitude, $attendance->clock_in_longitude) ?? '-')
                    : '00:00',
                'Punched Out' => ($attendance->clock_out != '00:00:00')
                    ? auth()->user()->timeFormat($attendance->clock_out)
                        . "\nODO: " . ($attendance->end_ride ?? '-')
                        . "\nLocation: " . ($attendance->getAddress($attendance->clock_out_latitude, $attendance->clock_out_longitude) ?? '-')
                    : '00:00',
                'Total KM Travelled' => $attendance->total_ride ?? '-',
                'Status' => $attendance->status ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Employee',
            'Date',
            'Punched In',
            'Punched Out',
            'Total KM Travelled',
            'Status',
        ];
    }
}
