<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Employee;
use App\Models\ApplicationLog;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Str;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;


class ApplicationLogExport implements FromCollection, WithHeadings
{
    protected $applicationLogs;

    public function __construct($applicationLogs)
    {
        $this->applicationLogs = $applicationLogs;
    }

    public function collection()
    {
        if ($this->applicationLogs->isEmpty()) {
            return collect([
                ['No records found', '', '', '', '', '', '']
            ]);
        }
        

        return $this->applicationLogs->map(function ($log) {
            
        // $type = filter_var($log->application_name, FILTER_VALIDATE_URL) ? 'web' : 'app';
        $type = (Str::contains($log->application_name, '.') || filter_var($log->application_name, FILTER_VALIDATE_URL)) ? 'web' : 'app';


            return [
                'Employee Name'     => $log->user->name ?? '',
                'Employee ID'       => $log->user->employee->employee_id ?? '',
                'Team'              => $log->user->employee->team->name ?? '',
                'Type' => $log->application_name . ' - ' . $type,
                'Details'           => $log->url  ?? '-',
                // You can decide whether to calculate or format screen_time if needed
                'Usage Duration'    => $log->screen_time ?? '-',
                'Active Duration'   => $log->screen_time ?? '-',  // You might have a separate field for this
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Employee ID',
            'Team',
            'Type',
            'Details',
            'Usage Duration',
            'Active Duration',
        ];
    }
}