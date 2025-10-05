<?php

namespace App\Controllers\Counselor;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard Konselor',
            'user' => session()->get()
        ];

        return view('counselor/dashboard', $data);
    }
}
