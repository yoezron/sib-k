<?php

/**
 * File Path: app/Controllers/Admin/RoleController.php
 *
 * Role Controller
 * Menyediakan placeholder untuk modul manajemen role
 *
 * @package    SIB-K
 * @subpackage Controllers/Admin
 * @category   Role Management
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class RoleController extends BaseController
{
    /**
     * Dummy roles data untuk kebutuhan tampilan sementara
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getRoles(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Administrator',
                'description' => 'Memiliki akses penuh ke seluruh sistem',
                'permissions' => 24,
            ],
            [
                'id' => 2,
                'name' => 'Koordinator BK',
                'description' => 'Mengelola data konseling dan laporan',
                'permissions' => 16,
            ],
            [
                'id' => 3,
                'name' => 'Guru BK',
                'description' => 'Mengakses data siswa dan sesi konseling',
                'permissions' => 12,
            ],
        ];
    }

    /**
     * Find dummy role by id
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    protected function findRole(int $id): ?array
    {
        foreach ($this->getRoles() as $role) {
            if ((int) $role['id'] === $id) {
                return $role;
            }
        }

        return null;
    }

    /**
     * Display roles list
     */
    public function index()
    {
        $data = [
            'title' => 'Manajemen Role',
            'li_1' => 'Admin',
            'li_2' => 'Role',
            'page_title' => 'Daftar Role',
            'roles' => $this->getRoles(),
        ];

        return view('admin/roles/index', $data);
    }

    /**
     * Display create role form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Role',
            'li_1' => 'Admin',
            'li_2' => 'Role',
            'page_title' => 'Tambah Role Baru',
            'mode' => 'create',
        ];

        return view('admin/roles/form', $data);
    }

    /**
     * Handle store role request
     */
    public function store()
    {
        return redirect()->to('admin/roles')
            ->with('info', 'Modul role belum terhubung dengan basis data. Silakan lengkapi implementasinya.');
    }

    /**
     * Display edit form
     */
    public function edit($id)
    {
        $role = $this->findRole((int) $id);

        if (!$role) {
            return redirect()->to('admin/roles')->with('error', 'Role tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Role',
            'li_1' => 'Admin',
            'li_2' => 'Role',
            'page_title' => 'Edit Role',
            'mode' => 'edit',
            'role' => $role,
        ];

        return view('admin/roles/form', $data);
    }

    /**
     * Handle update request
     */
    public function update($id)
    {
        return redirect()->to('admin/roles')
            ->with('info', 'Perubahan role belum disimpan karena modul masih berupa placeholder.');
    }

    /**
     * Handle delete request
     */
    public function delete($id)
    {
        return redirect()->to('admin/roles')
            ->with('info', 'Penghapusan role belum tersedia pada versi demo.');
    }

    /**
     * Display permissions form
     */
    public function permissions($id)
    {
        $role = $this->findRole((int) $id);

        if (!$role) {
            return redirect()->to('admin/roles')->with('error', 'Role tidak ditemukan.');
        }

        $availablePermissions = [
            'Kelola Pengguna',
            'Kelola Role',
            'Kelola Data Siswa',
            'Kelola Kelas',
            'Kelola Tahun Akademik',
            'Lihat Laporan',
        ];

        $data = [
            'title' => 'Pengaturan Hak Akses',
            'li_1' => 'Admin',
            'li_2' => 'Role',
            'page_title' => 'Pengaturan Hak Akses',
            'role' => $role,
            'available_permissions' => $availablePermissions,
        ];

        return view('admin/roles/permissions', $data);
    }

    /**
     * Handle assign permissions request
     */
    public function assignPermissions($id)
    {
        return redirect()->to('admin/roles/permissions/' . $id)
            ->with('info', 'Penyimpanan hak akses belum diaktifkan pada versi demo.');
    }
}
