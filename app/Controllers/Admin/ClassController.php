<?php

/**
 * File Path: app/Controllers/Admin/ClassController.php
 *
 * Class Controller
 * Placeholder untuk modul manajemen kelas
 *
 * @package    SIB-K
 * @subpackage Controllers/Admin
 * @category   Academic Management
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ClassController extends BaseController
{
    /**
     * Dummy classes data
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getClasses(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'X IPA 1',
                'homeroom' => 'Budi Santoso',
                'student_count' => 32,
            ],
            [
                'id' => 2,
                'name' => 'X IPA 2',
                'homeroom' => 'Siti Rahmawati',
                'student_count' => 30,
            ],
            [
                'id' => 3,
                'name' => 'X IPS 1',
                'homeroom' => 'Ahmad Yani',
                'student_count' => 28,
            ],
        ];
    }

    protected function findClass(int $id): ?array
    {
        foreach ($this->getClasses() as $class) {
            if ((int) $class['id'] === $id) {
                return $class;
            }
        }

        return null;
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Kelas',
            'li_1' => 'Admin',
            'li_2' => 'Kelas',
            'page_title' => 'Daftar Kelas',
            'classes' => $this->getClasses(),
        ];

        return view('admin/classes/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Kelas',
            'li_1' => 'Admin',
            'li_2' => 'Kelas',
            'page_title' => 'Tambah Kelas',
            'mode' => 'create',
        ];

        return view('admin/classes/form', $data);
    }

    public function store()
    {
        return redirect()->to('admin/classes')
            ->with('info', 'Penyimpanan data kelas belum diimplementasikan.');
    }

    public function edit($id)
    {
        $class = $this->findClass((int) $id);

        if (!$class) {
            return redirect()->to('admin/classes')->with('error', 'Data kelas tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Kelas',
            'li_1' => 'Admin',
            'li_2' => 'Kelas',
            'page_title' => 'Edit Kelas',
            'mode' => 'edit',
            'class' => $class,
        ];

        return view('admin/classes/form', $data);
    }

    public function update($id)
    {
        return redirect()->to('admin/classes')
            ->with('info', 'Perubahan data kelas belum diterapkan.');
    }

    public function delete($id)
    {
        return redirect()->to('admin/classes')
            ->with('info', 'Penghapusan kelas belum tersedia.');
    }

    public function detail($id)
    {
        $class = $this->findClass((int) $id);

        if (!$class) {
            return redirect()->to('admin/classes')->with('error', 'Data kelas tidak ditemukan.');
        }

        $students = [
            ['name' => 'Andi Wijaya', 'nis' => '123001'],
            ['name' => 'Rina Lestari', 'nis' => '123002'],
            ['name' => 'Dewi Anggraini', 'nis' => '123003'],
        ];

        $data = [
            'title' => 'Detail Kelas',
            'li_1' => 'Admin',
            'li_2' => 'Kelas',
            'page_title' => 'Detail Kelas',
            'class' => $class,
            'students' => $students,
        ];

        return view('admin/classes/detail', $data);
    }
}
