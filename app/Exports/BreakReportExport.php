<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Employee;
use App\Models\BreakType;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class BreakReportExport implements FromCollection, WithHeadings
{
    protected $breaks;

    public function __construct($breaks)
    {
        $this->breaks = $breaks;
    }

    public function collection()
{
    
        if ($this->breaks->isEmpty()) {
        return collect([
            ['No records found', '', '', '', '', '']
        ]);
    }


    return $this->breaks->map(function ($break) {
        $startTime = $break->break_started_at
            ? \Carbon\Carbon::parse($break->break_started_at)->format('h:i A')
            : '';

        $endTime = $break->break_ended_at
            ? \Carbon\Carbon::parse($break->break_ended_at)->format('h:i A')
            : 'Ongoing';

        $timeRange = $startTime . ' - ' . $endTime;

        return [
            $break->employee->user->name ?? '',
            $break->employee->team->name ?? '',
            $break->breakType->break_name ?? '',
            $timeRange,
            $break->created_at->toDateString(),
            $break->duration,
        ];
    });
}


   public function headings(): array
{
    return ['User', 'Team', 'Break Type', 'Time Range', 'Date', 'Duration'];
}

}
