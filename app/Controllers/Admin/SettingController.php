<?php

/**
 * File Path: app/Controllers/Admin/SettingController.php
 *
 * Setting Controller
 * Placeholder untuk modul pengaturan sistem
 *
 * @package    SIB-K
 * @subpackage Controllers/Admin
 * @category   Settings
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class SettingController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Pengaturan Sistem',
            'li_1' => 'Admin',
            'li_2' => 'Pengaturan',
            'page_title' => 'Pengaturan Sistem',
            'settings' => [
                'app_name' => 'Sistem Informasi BK',
                'school_name' => 'SMA Negeri Contoh',
                'contact_email' => 'admin@sekolah.sch.id',
            ],
        ];

        return view('admin/settings/index', $data);
    }

    public function update()
    {
        return redirect()->to('admin/settings')
            ->with('info', 'Penyimpanan pengaturan belum diaktifkan pada versi demo.');
    }
}
