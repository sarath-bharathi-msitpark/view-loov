<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Employee;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Auth;

class UsersExport implements FromView
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function view(): View
    {
        return view('company.settings.userexport', [
            'employees' => $this->employees
        ]);
        
    }
}
