<?php

namespace App\Controllers\Parent;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard Orang Tua',
            'user' => session()->get()
        ];

        return view('parent/dashboard', $data);
    }
}
