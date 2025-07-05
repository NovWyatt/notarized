<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $adminUsers = User::role('admin')->count();
        $specialistUsers = User::role('specialist')->count();
        
        return view('admin.dashboard', compact('totalUsers', 'adminUsers', 'specialistUsers'));
    }

    public function systemSettings()
    {
        // Chỉ admin mới truy cập được
        return view('admin.settings');
    }

    public function systemLogs()
    {
        // Chỉ admin mới xem được logs
        return view('admin.logs');
    }
}