<?php

/**
 * File Path: app/Controllers/Admin/AcademicYearController.php
 *
 * Academic Year Controller
 * Placeholder untuk modul tahun akademik
 *
 * @package    SIB-K
 * @subpackage Controllers/Admin
 * @category   Academic Management
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AcademicYearController extends BaseController
{
    /**
     * Dummy academic year data
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getAcademicYears(): array
    {
        return [
            [
                'id' => 1,
                'name' => '2023/2024',
                'semester' => 'Ganjil',
                'start_date' => '2023-07-01',
                'end_date' => '2023-12-31',
                'is_active' => false,
            ],
            [
                'id' => 2,
                'name' => '2023/2024',
                'semester' => 'Genap',
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-30',
                'is_active' => true,
            ],
        ];
    }

    /**
     * Find academic year by id
     */
    protected function findAcademicYear(int $id): ?array
    {
        foreach ($this->getAcademicYears() as $year) {
            if ((int) $year['id'] === $id) {
                return $year;
            }
        }

        return null;
    }

    public function index()
    {
        $data = [
            'title' => 'Tahun Akademik',
            'li_1' => 'Admin',
            'li_2' => 'Tahun Akademik',
            'page_title' => 'Manajemen Tahun Akademik',
            'academic_years' => $this->getAcademicYears(),
        ];

        return view('admin/academic_years/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Tahun Akademik',
            'li_1' => 'Admin',
            'li_2' => 'Tahun Akademik',
            'page_title' => 'Tambah Tahun Akademik',
            'mode' => 'create',
        ];

        return view('admin/academic_years/form', $data);
    }

    public function store()
    {
        return redirect()->to('admin/academic-years')
            ->with('info', 'Penyimpanan tahun akademik belum diimplementasikan.');
    }

    public function edit($id)
    {
        $year = $this->findAcademicYear((int) $id);

        if (!$year) {
            return redirect()->to('admin/academic-years')->with('error', 'Tahun akademik tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Tahun Akademik',
            'li_1' => 'Admin',
            'li_2' => 'Tahun Akademik',
            'page_title' => 'Edit Tahun Akademik',
            'mode' => 'edit',
            'academic_year' => $year,
        ];

        return view('admin/academic_years/form', $data);
    }

    public function update($id)
    {
        return redirect()->to('admin/academic-years')
            ->with('info', 'Perubahan tahun akademik belum diterapkan.');
    }

    public function delete($id)
    {
        return redirect()->to('admin/academic-years')
            ->with('info', 'Penghapusan tahun akademik belum tersedia.');
    }

    public function setActive($id)
    {
        return redirect()->to('admin/academic-years')
            ->with('info', 'Penetapan tahun akademik aktif belum diaktifkan pada versi demo.');
    }
}
