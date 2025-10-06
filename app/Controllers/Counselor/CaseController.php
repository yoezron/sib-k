<?php

/**
 * File Path: app/Controllers/Counselor/CaseController.php
 * 
 * Case Controller
 * Controller untuk mengelola case/violation dan sanksi siswa
 * 
 * @package    SIB-K
 * @subpackage Controllers/Counselor
 * @category   Controller
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Controllers\Counselor;

use App\Controllers\BaseController;
use App\Services\ViolationService;
use App\Models\ViolationModel;
use App\Models\ViolationCategoryModel;
use App\Models\SanctionModel;
use App\Models\StudentModel;
use App\Models\ClassModel;

class CaseController extends BaseController
{
    protected $violationService;
    protected $violationModel;
    protected $categoryModel;
    protected $sanctionModel;
    protected $studentModel;
    protected $classModel;
    protected $db;

    public function __construct()
    {
        $this->violationService = new ViolationService();
        $this->violationModel = new ViolationModel();
        $this->categoryModel = new ViolationCategoryModel();
        $this->sanctionModel = new SanctionModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Display list of violations/cases
     * 
     * @return string
     */
    public function index()
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Get filters from request
        $filters = [
            'status' => $this->request->getGet('status'),
            'severity_level' => $this->request->getGet('severity_level'),
            'student_id' => $this->request->getGet('student_id'),
            'category_id' => $this->request->getGet('category_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'is_repeat_offender' => $this->request->getGet('is_repeat_offender'),
            'parent_notified' => $this->request->getGet('parent_notified'),
            'search' => $this->request->getGet('search'),
        ];

        // If Guru BK (not koordinator), only show their handled cases
        if (is_guru_bk() && !is_koordinator()) {
            $filters['handled_by'] = auth_id();
        }

        // Get violations with filters
        $data['violations'] = $this->violationService->getViolations($filters);

        // Get filter options
        $data['students'] = $this->getActiveStudents();
        $data['categories'] = $this->violationService->getActiveCategories();
        $data['filters'] = $filters;

        // Get statistics
        $data['stats'] = $this->violationService->getDashboardStats($filters);

        // Page metadata
        $data['title'] = 'Manajemen Kasus & Pelanggaran';
        $data['pageTitle'] = 'Daftar Kasus Pelanggaran';
        $data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'url' => base_url('counselor/dashboard')],
            ['title' => 'Kasus & Pelanggaran', 'url' => '#', 'active' => true],
        ];

        return view('counselor/cases/index', $data);
    }

    /**
     * Show create violation form
     * 
     * @return string
     */
    public function create()
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Get data for form
        $data['students'] = $this->getActiveStudents();
        $data['categories'] = $this->violationService->getCategoriesGrouped();
        $data['classes'] = $this->getActiveClasses();

        // Page metadata
        $data['title'] = 'Tambah Pelanggaran Baru';
        $data['pageTitle'] = 'Lapor Pelanggaran';
        $data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'url' => base_url('counselor/dashboard')],
            ['title' => 'Kasus & Pelanggaran', 'url' => base_url('counselor/cases')],
            ['title' => 'Tambah', 'url' => '#', 'active' => true],
        ];

        return view('counselor/cases/create', $data);
    }

    /**
     * Store new violation
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function store()
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $data = $this->request->getPost();

        // Set reporter
        $data['reported_by'] = auth_id();

        // Set handler (optional - can be assigned later)
        if (empty($data['handled_by'])) {
            $data['handled_by'] = auth_id(); // Self-assign by default
        }

        try {
            // Create violation
            $result = $this->violationService->createViolation($data);

            if (!$result['success']) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $result['errors'] ?? [$result['message']]);
            }

            return redirect()->to('counselor/cases/detail/' . $result['violation_id'])
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            log_message('error', 'Error storing violation: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show violation detail
     * 
     * @param int $id
     * @return string
     */
    public function detail($id)
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Get violation detail
        $violation = $this->violationService->getViolationDetail($id);

        if (!$violation) {
            return redirect()->to('counselor/cases')
                ->with('error', 'Data pelanggaran tidak ditemukan');
        }

        // Check access - Guru BK can only view their handled cases
        if (is_guru_bk() && !is_koordinator()) {
            if ($violation['handled_by'] != auth_id() && $violation['reported_by'] != auth_id()) {
                return redirect()->to('counselor/cases')
                    ->with('error', 'Anda tidak memiliki akses ke kasus ini');
            }
        }

        $data['violation'] = $violation;

        // Get student violation history
        $history = $this->violationService->getStudentViolationHistory($violation['student_id']);
        $data['student_history'] = $history;

        // Get common sanction types for dropdown
        $data['sanction_types'] = $this->sanctionModel->getCommonSanctionTypes();

        // Page metadata
        $data['title'] = 'Detail Pelanggaran';
        $data['pageTitle'] = 'Detail Kasus - ' . $violation['category_name'];
        $data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'url' => base_url('counselor/dashboard')],
            ['title' => 'Kasus & Pelanggaran', 'url' => base_url('counselor/cases')],
            ['title' => 'Detail', 'url' => '#', 'active' => true],
        ];

        return view('counselor/cases/detail', $data);
    }

    /**
     * Update violation
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Get violation
        $violation = $this->violationModel->find($id);

        if (!$violation) {
            return redirect()->to('counselor/cases')
                ->with('error', 'Data pelanggaran tidak ditemukan');
        }

        // Check access
        if (is_guru_bk() && !is_koordinator()) {
            if ($violation['handled_by'] != auth_id() && $violation['reported_by'] != auth_id()) {
                return redirect()->to('counselor/cases')
                    ->with('error', 'Anda tidak memiliki akses untuk mengubah kasus ini');
            }
        }

        $data = $this->request->getPost();

        try {
            // Update violation
            $result = $this->violationService->updateViolation($id, $data);

            if (!$result['success']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message']);
            }

            return redirect()->to('counselor/cases/detail/' . $id)
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            log_message('error', 'Error updating violation: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete violation
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Only koordinator can delete
        if (!is_koordinator()) {
            return redirect()->to('counselor/cases')
                ->with('error', 'Hanya Koordinator BK yang dapat menghapus data pelanggaran');
        }

        try {
            $result = $this->violationService->deleteViolation($id);

            if (!$result['success']) {
                return redirect()->back()
                    ->with('error', $result['message']);
            }

            return redirect()->to('counselor/cases')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            log_message('error', 'Error deleting violation: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Add sanction to violation
     * 
     * @param int $violationId
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function addSanction($violationId)
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Validate violation exists
        $violation = $this->violationModel->find($violationId);

        if (!$violation) {
            return redirect()->to('counselor/cases')
                ->with('error', 'Data pelanggaran tidak ditemukan');
        }

        $data = $this->request->getPost();
        $data['violation_id'] = $violationId;
        $data['assigned_by'] = auth_id();

        // Validate input
        $rules = [
            'sanction_type' => 'required|max_length[100]',
            'sanction_date' => 'required|valid_date',
            'description' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            // Save sanction
            $sanctionId = $this->sanctionModel->insert($data);

            if (!$sanctionId) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->sanctionModel->errors());
            }

            // Update violation status if still "Dilaporkan"
            if ($violation['status'] === 'Dilaporkan') {
                $this->violationModel->update($violationId, ['status' => 'Dalam Proses']);
            }

            return redirect()->to('counselor/cases/detail/' . $violationId)
                ->with('success', 'Sanksi berhasil ditambahkan');
        } catch (\Exception $e) {
            log_message('error', 'Error adding sanction: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to parent
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function notifyParent($id)
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        try {
            $result = $this->violationService->notifyParent($id);

            if (!$result['success']) {
                return redirect()->back()
                    ->with('error', $result['message']);
            }

            return redirect()->back()
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            log_message('error', 'Error notifying parent: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get active students for dropdown
     * 
     * @return array
     */
    private function getActiveStudents()
    {
        return $this->studentModel
            ->select('students.id, students.nisn, students.nis, users.full_name, classes.class_name')
            ->join('users', 'users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('students.status', 'Aktif')
            ->orderBy('users.full_name', 'ASC')
            ->findAll();
    }

    /**
     * Get active classes for dropdown
     * 
     * @return array
     */
    private function getActiveClasses()
    {
        return $this->classModel
            ->where('is_active', 1)
            ->orderBy('class_name', 'ASC')
            ->findAll();
    }
}
