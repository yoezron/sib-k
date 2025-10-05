<?php

namespace App\Controllers\HomeroomTeacher;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard Wali Kelas',
            'user' => session()->get()
        ];

        return view('homeroom_teacher/dashboard', $data);
    }
}
