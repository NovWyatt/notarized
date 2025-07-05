<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpecialistController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:specialist|admin');
    }

    public function dashboard()
    {
        return view('specialist.dashboard');
    }

    public function reports()
    {
        // Cả specialist và admin đều xem được
        return view('specialist.reports');
    }
}