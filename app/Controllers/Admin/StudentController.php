<?php

/**
 * File Path: app/Controllers/Admin/StudentController.php
 * 
 * Student Controller
 * Handle CRUD operations untuk Student management
 * 
 * @package    SIB-K
 * @subpackage Controllers/Admin
 * @category   Student Management
 * @author     Development Team
 * @created    2025-01-05
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\StudentService;
use App\Validation\StudentValidation;

class StudentController extends BaseController
{
    protected $studentService;

    public function __construct()
    {
        $this->studentService = new StudentService();
    }

    /**
     * Display students list
     * 
     * @return string
     */
    public function index()
    {
        // Get filters from request
        $filters = [
            'class_id' => $this->request->getGet('class_id'),
            'grade_level' => $this->request->getGet('grade_level'),
            'status' => $this->request->getGet('status'),
            'gender' => $this->request->getGet('gender'),
            'search' => $this->request->getGet('search'),
            'order_by' => $this->request->getGet('order_by') ?? 'students.created_at',
            'order_dir' => $this->request->getGet('order_dir') ?? 'DESC',
        ];

        // Get students with pagination
        $perPage = 15;
        $studentsData = $this->studentService->getAllStudents($filters, $perPage);

        // Get filter options
        $classes = $this->studentService->getAvailableClasses();

        // Get statistics
        $stats = $this->studentService->getStudentStatistics();

        $data = [
            'title' => 'Manajemen Siswa',
            'page_title' => 'Daftar Siswa',
            'breadcrumb' => [
                ['title' => 'Admin', 'link' => base_url('admin/dashboard')],
                ['title' => 'Siswa', 'link' => null],
            ],
            'students' => $studentsData['students'],
            'pager' => $studentsData['pager'],
            'classes' => $classes,
            'stats' => $stats,
            'filters' => $filters,
            'gender_options' => StudentValidation::getGenderOptions(),
            'status_options' => StudentValidation::getStatusOptions(),
        ];

        return view('admin/students/index', $data);
    }

    /**
     * Display create student form
     * 
     * @return string
     */
    public function create()
    {
        // Get dropdown options
        $classes = $this->studentService->getAvailableClasses();
        $parents = $this->studentService->getAvailableParents();

        $data = [
            'title' => 'Tambah Siswa Baru',
            'page_title' => 'Tambah Siswa',
            'breadcrumb' => [
                ['title' => 'Admin', 'link' => base_url('admin/dashboard')],
                ['title' => 'Siswa', 'link' => base_url('admin/students')],
                ['title' => 'Tambah', 'link' => null],
            ],
            'classes' => $classes,
            'parents' => $parents,
            'gender_options' => StudentValidation::getGenderOptions(),
            'religion_options' => StudentValidation::getReligionOptions(),
            'status_options' => StudentValidation::getStatusOptions(),
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/students/create', $data);
    }

    /**
     * Store new student
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function store()
    {
        // Determine if creating with new user or existing user
        $createWithUser = $this->request->getPost('create_with_user') == '1';

        if ($createWithUser) {
            // Validate with user fields
            $rules = StudentValidation::createWithUserRules();
        } else {
            // Validate student only
            $rules = StudentValidation::createRules();
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Sanitize input
        $data = StudentValidation::sanitizeInput($this->request->getPost());

        // Create student
        if ($createWithUser) {
            $result = $this->studentService->createStudentWithUser($data);
        } else {
            $result = $this->studentService->createStudent($data);
        }

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()->to('admin/students')
            ->with('success', $result['message']);
    }

    /**
     * Display student profile
     * 
     * @param int $id
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function profile($id)
    {
        $student = $this->studentService->getStudentById($id);

        if (!$student) {
            return redirect()->to('admin/students')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        $data = [
            'title' => 'Profil Siswa',
            'page_title' => 'Profil Siswa',
            'breadcrumb' => [
                ['title' => 'Admin', 'link' => base_url('admin/dashboard')],
                ['title' => 'Siswa', 'link' => base_url('admin/students')],
                ['title' => 'Profil', 'link' => null],
            ],
            'student' => $student,
        ];

        return view('admin/students/profile', $data);
    }

    /**
     * Display edit student form
     * 
     * @param int $id
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function edit($id)
    {
        $student = $this->studentService->getStudentById($id);

        if (!$student) {
            return redirect()->to('admin/students')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Get dropdown options
        $classes = $this->studentService->getAvailableClasses();
        $parents = $this->studentService->getAvailableParents();

        $data = [
            'title' => 'Edit Data Siswa',
            'page_title' => 'Edit Siswa',
            'breadcrumb' => [
                ['title' => 'Admin', 'link' => base_url('admin/dashboard')],
                ['title' => 'Siswa', 'link' => base_url('admin/students')],
                ['title' => 'Edit', 'link' => null],
            ],
            'student' => $student,
            'classes' => $classes,
            'parents' => $parents,
            'gender_options' => StudentValidation::getGenderOptions(),
            'religion_options' => StudentValidation::getReligionOptions(),
            'status_options' => StudentValidation::getStatusOptions(),
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/students/edit', $data);
    }

    /**
     * Update student data
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        // Check if student exists
        $student = $this->studentService->getStudentById($id);
        if (!$student) {
            return redirect()->to('admin/students')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Validate input
        $rules = StudentValidation::updateRules($id);

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Sanitize input
        $data = StudentValidation::sanitizeInput($this->request->getPost());

        // Update student
        $result = $this->studentService->updateStudent($id, $data);

        if (!$result['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()->to('admin/students')
            ->with('success', $result['message']);
    }

    /**
     * Delete student
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        // Delete student
        $result = $this->studentService->deleteStudent($id);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', $result['message']);
        }

        return redirect()->to('admin/students')
            ->with('success', $result['message']);
    }

    /**
     * Change student class
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function changeClass($id)
    {
        $newClassId = $this->request->getPost('class_id');

        if (empty($newClassId)) {
            return redirect()->back()
                ->with('error', 'Kelas baru harus dipilih');
        }

        // Change class
        $result = $this->studentService->changeClass($id, $newClassId);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', $result['message']);
        }

        return redirect()->back()
            ->with('success', $result['message']);
    }

    /**
     * Export students to CSV
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function export()
    {
        // Get all students with filters
        $filters = [
            'class_id' => $this->request->getGet('class_id'),
            'grade_level' => $this->request->getGet('grade_level'),
            'status' => $this->request->getGet('status'),
            'gender' => $this->request->getGet('gender'),
            'search' => $this->request->getGet('search'),
        ];

        $studentsData = $this->studentService->getAllStudents($filters, 10000); // Get all

        // Prepare data for export
        $exportData = [];
        foreach ($studentsData['students'] as $student) {
            $exportData[] = [
                'ID' => $student['id'],
                'NISN' => $student['nisn'],
                'NIS' => $student['nis'],
                'Nama Lengkap' => $student['full_name'],
                'Username' => $student['username'],
                'Email' => $student['email'],
                'Jenis Kelamin' => $student['gender'] == 'L' ? 'Laki-laki' : 'Perempuan',
                'Kelas' => $student['class_name'] ?? '-',
                'Tingkat' => $student['grade_level'] ?? '-',
                'Tempat Lahir' => $student['birth_place'] ?? '-',
                'Tanggal Lahir' => $student['birth_date'] ? date('d/m/Y', strtotime($student['birth_date'])) : '-',
                'Agama' => $student['religion'] ?? '-',
                'Alamat' => $student['address'] ?? '-',
                'Telepon' => $student['phone'] ?? '-',
                'Status' => $student['status'],
                'Poin Pelanggaran' => $student['total_violation_points'],
                'Tanggal Masuk' => $student['admission_date'] ? date('d/m/Y', strtotime($student['admission_date'])) : '-',
                'Terdaftar' => date('d/m/Y H:i', strtotime($student['created_at'])),
            ];
        }

        // Create CSV
        $filename = 'students_export_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add BOM for Excel UTF-8 support
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Add headers
        if (!empty($exportData)) {
            fputcsv($output, array_keys($exportData[0]));
        }

        // Add data
        foreach ($exportData as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Search students via AJAX (for autocomplete)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');

        if (empty($keyword)) {
            return $this->response->setJSON([
                'results' => []
            ]);
        }

        $filters = [
            'search' => $keyword,
        ];

        $studentsData = $this->studentService->getAllStudents($filters, 10);

        $results = [];
        foreach ($studentsData['students'] as $student) {
            $results[] = [
                'id' => $student['id'],
                'text' => $student['full_name'] . ' (' . $student['nisn'] . ')',
                'nisn' => $student['nisn'],
                'nis' => $student['nis'],
                'class' => $student['class_name'] ?? '-',
                'status' => $student['status'],
            ];
        }

        return $this->response->setJSON([
            'results' => $results
        ]);
    }

    /**
     * Get students by class via AJAX
     * 
     * @param int $classId
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getByClass($classId)
    {
        $filters = [
            'class_id' => $classId,
            'status' => 'Aktif',
        ];

        $studentsData = $this->studentService->getAllStudents($filters, 100);

        $students = [];
        foreach ($studentsData['students'] as $student) {
            $students[] = [
                'id' => $student['id'],
                'user_id' => $student['user_id'],
                'nisn' => $student['nisn'],
                'nis' => $student['nis'],
                'full_name' => $student['full_name'],
                'gender' => $student['gender'],
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'students' => $students,
        ]);
    }

    /**
     * Display import students form
     * 
     * @return string
     */
    public function import()
    {
        // Get dropdown options
        $classes = $this->studentService->getAvailableClasses();

        $data = [
            'title' => 'Import Data Siswa',
            'page_title' => 'Import Siswa',
            'breadcrumb' => [
                ['title' => 'Admin', 'link' => base_url('admin/dashboard')],
                ['title' => 'Siswa', 'link' => base_url('admin/students')],
                ['title' => 'Import', 'link' => null],
            ],
            'classes' => $classes,
        ];

        return view('admin/students/import', $data);
    }

    /**
     * Download import template
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function downloadTemplate()
    {
        $filename = 'template_import_siswa.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add BOM for Excel UTF-8 support
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Add headers
        $headers = [
            'NISN',
            'NIS',
            'Nama Lengkap',
            'Username',
            'Email',
            'Password',
            'Jenis Kelamin (L/P)',
            'Tempat Lahir',
            'Tanggal Lahir (YYYY-MM-DD)',
            'Agama',
            'Alamat',
            'Telepon',
            'Tanggal Masuk (YYYY-MM-DD)',
        ];

        fputcsv($output, $headers);

        // Add sample data
        $sample = [
            '1234567890',
            'NIS001',
            'Contoh Nama Siswa',
            'siswa001',
            'siswa001@example.com',
            'password123',
            'L',
            'Bandung',
            '2010-01-15',
            'Islam',
            'Jl. Contoh No. 123',
            '081234567890',
            '2024-07-15',
        ];

        fputcsv($output, $sample);

        fclose($output);
        exit;
    }

    /**
     * Get student statistics via AJAX
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getStats()
    {
        $stats = $this->studentService->getStudentStatistics();

        return $this->response->setJSON([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
