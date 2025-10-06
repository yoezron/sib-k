<?php

/**
 * File Path: app/Controllers/ProfileController.php
 *
 * Profile Controller
 * Menangani tampilan profil pengguna sederhana
 *
 * @package    SIB-K
 * @subpackage Controllers
 * @category   Profile
 */

namespace App\Controllers;

class ProfileController extends BaseController
{
    /**
     * Display the profile page
     *
     * @return string
     */
    public function index()
    {
        $data = [
            'title' => 'Profil Pengguna',
            'li_1' => 'Beranda',
            'li_2' => 'Profil',
            'page_title' => 'Profil Pengguna',
        ];

        return view('profile/index', $data);
    }
}
