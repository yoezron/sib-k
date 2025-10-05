<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard Siswa',
            'user' => session()->get()
        ];

        return view('student/dashboard', $data);
    }
}
