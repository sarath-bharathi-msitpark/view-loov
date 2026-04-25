<?php

namespace App\Http\Controllers\Web\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskManagementController extends Controller
{
    public function index()
    {
        return view('company.task_management.index');
    }

    public function taskWiseScreenShot()
    {
        return view('company.task_management.screenshot');
    }

    public function newsLetter()
    {
        return view('company.task_management.news_letter');
    }

    public function show()
    {
        return view('company.task_management.task_details');
    }

    public function taskStage()
    {
        return view('company.task_management.task_stage');
    }
}
