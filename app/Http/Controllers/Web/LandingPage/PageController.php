<?php

namespace App\Http\Controllers\Web\LandingPage;
use App\Http\Controllers\Controller;

use App\Models\Plan;
use App\Models\User;
use App\Models\Utility;
use File;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function terms()
    {
        return view('pages.terms');
    }
    
    public function privacy_policy()
    {
        return view('pages.privacy_policy');
    }
}
