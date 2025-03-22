<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'superadmin':
                return view('superadmin.dashboard');
            case 'admin':
                return view('admin.dashboard');
            case 'staff':
                return view('staff.dashboard');
            case 'employee':
                return view('employee.dashboard');
            case 'applicant':
                return view('applicant.dashboard');
            default:
                abort(403, 'Unauthorized access');
        }
    }
}
