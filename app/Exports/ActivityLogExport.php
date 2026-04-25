<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActivityLogExport implements FromCollection, WithHeadings
{
    protected $incidents;

    public function __construct($incidents)
    {
        $this->incidents = $incidents;
    }

    public function collection()
    {
        $data = collect();

        foreach ($this->incidents as $incident) {
            $data->push([
                'Employee Name' => $incident->user->name ?? 'N/A',
                'Employee ID' => $incident->user->employee->employee_id ?? 'N/A',
                'Key Presses' => $incident->total_keyboard_actions,
                'Mouse Clicks' => $incident->total_mouse_actions,
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Employee ID',
            'Key Presses',
            'Mouse Clicks',
        ];
    }
}
