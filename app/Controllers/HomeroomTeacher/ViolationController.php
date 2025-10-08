<?php

/**
 * File Path: app/Controllers/HomeroomTeacher/ViolationController.php
 * 
 * Homeroom Teacher Violation Controller
 * Mengelola pelanggaran siswa di kelas yang diampu oleh wali kelas
 * 
 * @package    SIB-K
 * @subpackage Controllers/HomeroomTeacher
 * @category   Controller
 * @author     Development Team
 * @created    2025-01-07
 */

namespace App\Controllers\HomeroomTeacher;

use App\Controllers\BaseController;
use App\Models\ClassModel;
use App\Models\StudentModel;
use App\Models\ViolationModel;
use App\Models\ViolationCategoryModel;

class ViolationController extends BaseController
{
    protected $classModel;
    protected $studentModel;
    protected $violationModel;
    protected $categoryModel;
    protected $db;
    protected $validation;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->studentModel = new StudentModel();
        $this->violationModel = new ViolationModel();
        $this->categoryModel = new ViolationCategoryModel();
        $this->db = \Config\Database::connect();
        $this->validation = \Config\Services::validation();

        // Load helpers
        helper(['permission', 'date', 'response', 'form']);
    }

    /**
     * Display list of violations
     * 
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function index()
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if user is homeroom teacher
        if (!is_homeroom_teacher()) {
            return redirect()->to(get_dashboard_url())->with('error', 'Akses ditolak');
        }

        $userId = current_user_id();

        // Get homeroom teacher's class
        $class = $this->getHomeroomClass($userId);

        if (!$class) {
            return redirect()->to('/homeroom/dashboard')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Get filter parameters
        $filters = [
            'student_id' => $this->request->getGet('student_id'),
            'category_id' => $this->request->getGet('category_id'),
            'severity' => $this->request->getGet('severity'),
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'status' => $this->request->getGet('status'),
            'search' => $this->request->getGet('search'),
        ];

        // Get violations with filters
        $violations = $this->getViolations($class['id'], $filters);

        // Get students in class for filter
        $students = $this->getClassStudents($class['id']);

        // Get violation categories for filter
        $categories = $this->categoryModel
            ->where('deleted_at', null)
            ->orderBy('category_name', 'ASC')
            ->findAll();

        // Prepare data for view
        $data = [
            'title' => 'Daftar Pelanggaran',
            'pageTitle' => 'Daftar Pelanggaran',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('homeroom/dashboard')],
                ['title' => 'Pelanggaran', 'url' => '#', 'active' => true],
            ],
            'class' => $class,
            'violations' => $violations,
            'students' => $students,
            'categories' => $categories,
            'filters' => $filters,
            'currentUser' => current_user(),
        ];

        return view('homeroom_teacher/violations/index', $data);
    }

    /**
     * Show create violation form
     * 
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        // Check authentication
        if (!is_logged_in() || !is_homeroom_teacher()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userId = current_user_id();

        // Get homeroom teacher's class
        $class = $this->getHomeroomClass($userId);

        if (!$class) {
            return redirect()->to('/homeroom/dashboard')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Get students in class
        $students = $this->getClassStudents($class['id']);

        // Get violation categories
        $categories = $this->categoryModel
            ->where('deleted_at', null)
            ->orderBy('severity', 'ASC')
            ->orderBy('category_name', 'ASC')
            ->findAll();

        // Group categories by severity
        $groupedCategories = [
            'Ringan' => [],
            'Sedang' => [],
            'Berat' => [],
        ];

        foreach ($categories as $category) {
            $severity = $category['severity'] ?? 'Sedang';
            $groupedCategories[$severity][] = $category;
        }

        // Prepare data for view
        $data = [
            'title' => 'Tambah Pelanggaran',
            'pageTitle' => 'Tambah Pelanggaran',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('homeroom/dashboard')],
                ['title' => 'Pelanggaran', 'url' => base_url('homeroom/violations')],
                ['title' => 'Tambah', 'url' => '#', 'active' => true],
            ],
            'class' => $class,
            'students' => $students,
            'categories' => $categories,
            'groupedCategories' => $groupedCategories,
            'currentUser' => current_user(),
            'validation' => $this->validation,
        ];

        return view('homeroom_teacher/violations/create', $data);
    }

    /**
     * Store new violation
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function store()
    {
        // Check authentication
        if (!is_logged_in() || !is_homeroom_teacher()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userId = current_user_id();

        // Get homeroom teacher's class
        $class = $this->getHomeroomClass($userId);

        if (!$class) {
            return redirect()->to('/homeroom/dashboard')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Validation rules
        $rules = [
            'student_id' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Siswa harus dipilih',
                    'numeric' => 'Siswa tidak valid',
                ]
            ],
            'category_id' => [
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'Kategori pelanggaran harus dipilih',
                    'numeric' => 'Kategori tidak valid',
                ]
            ],
            'violation_date' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal pelanggaran harus diisi',
                    'valid_date' => 'Format tanggal tidak valid',
                ]
            ],
            'violation_time' => [
                'rules' => 'permit_empty|valid_time',
                'errors' => [
                    'valid_time' => 'Format waktu tidak valid',
                ]
            ],
            'location' => [
                'rules' => 'permit_empty|max_length[200]',
                'errors' => [
                    'max_length' => 'Lokasi maksimal 200 karakter',
                ]
            ],
            'description' => [
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => 'Deskripsi pelanggaran harus diisi',
                    'min_length' => 'Deskripsi minimal 10 karakter',
                ]
            ],
            'witness' => [
                'rules' => 'permit_empty|max_length[200]',
                'errors' => [
                    'max_length' => 'Saksi maksimal 200 karakter',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validation->getErrors())
                ->with('error', 'Mohon periksa kembali input Anda.');
        }

        // Verify student belongs to homeroom class
        $studentId = $this->request->getPost('student_id');
        $student = $this->studentModel->find($studentId);

        if (!$student || $student['class_id'] != $class['id']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Siswa tidak ditemukan atau bukan dari kelas Anda.');
        }

        try {
            // Prepare violation data
            $violationData = [
                'student_id' => $studentId,
                'category_id' => $this->request->getPost('category_id'),
                'violation_date' => $this->request->getPost('violation_date'),
                'violation_time' => $this->request->getPost('violation_time') ?: null,
                'location' => $this->request->getPost('location') ?: null,
                'description' => $this->request->getPost('description'),
                'witness' => $this->request->getPost('witness') ?: null,
                'reported_by' => $userId,
                'status' => 'Dilaporkan',
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Insert violation
            $violationId = $this->violationModel->insert($violationData);

            if ($violationId) {
                // Get category for notification
                $category = $this->categoryModel->find($violationData['category_id']);

                // Send notification to counselor (if exists)
                if ($class['counselor_id']) {
                    helper('notification');
                    send_notification(
                        $class['counselor_id'],
                        'Pelanggaran Baru Dilaporkan',
                        "Pelanggaran {$category['category_name']} dilaporkan oleh {$class['class_name']}",
                        'violation',
                        ['violation_id' => $violationId]
                    );
                }

                // Send notification to student's parent (if exists)
                if ($student['parent_id']) {
                    helper('notification');
                    send_notification(
                        $student['parent_id'],
                        'Pelanggaran Siswa',
                        "Anak Anda melakukan pelanggaran: {$category['category_name']}",
                        'violation',
                        ['violation_id' => $violationId]
                    );
                }

                // Log activity
                log_message('info', "[VIOLATION] New violation created by homeroom teacher. ID: {$violationId}, Student: {$studentId}, Category: {$violationData['category_id']}");

                return redirect()->to('/homeroom/violations')
                    ->with('success', 'Pelanggaran berhasil dilaporkan.');
            } else {
                throw new \Exception('Failed to insert violation');
            }
        } catch (\Exception $e) {
            log_message('error', '[VIOLATION STORE] Error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Show violation detail
     * 
     * @param int $id
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function detail($id)
    {
        // Check authentication
        if (!is_logged_in() || !is_homeroom_teacher()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userId = current_user_id();

        // Get homeroom teacher's class
        $class = $this->getHomeroomClass($userId);

        if (!$class) {
            return redirect()->to('/homeroom/dashboard')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Get violation detail
        $violation = $this->getViolationDetail($id);

        if (!$violation) {
            return redirect()->to('/homeroom/violations')
                ->with('error', 'Data pelanggaran tidak ditemukan.');
        }

        // Verify violation belongs to homeroom class
        if ($violation['class_id'] != $class['id']) {
            return redirect()->to('/homeroom/violations')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        // Get sanctions for this violation
        $sanctions = $this->getViolationSanctions($id);

        // Get student's violation history
        $studentHistory = $this->getStudentViolationHistory($violation['student_id'], 5);

        // Prepare data for view
        $data = [
            'title' => 'Detail Pelanggaran',
            'pageTitle' => 'Detail Pelanggaran',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('homeroom/dashboard')],
                ['title' => 'Pelanggaran', 'url' => base_url('homeroom/violations')],
                ['title' => 'Detail', 'url' => '#', 'active' => true],
            ],
            'class' => $class,
            'violation' => $violation,
            'sanctions' => $sanctions,
            'studentHistory' => $studentHistory,
            'currentUser' => current_user(),
        ];

        return view('homeroom_teacher/violations/detail', $data);
    }

    /**
     * Get homeroom teacher's class
     * 
     * @param int $userId
     * @return array|null
     */
    private function getHomeroomClass($userId)
    {
        try {
            return $this->db->table('classes')
                ->select('classes.*, academic_years.year_name, academic_years.semester')
                ->join('academic_years', 'academic_years.id = classes.academic_year_id')
                ->where('classes.homeroom_teacher_id', $userId)
                ->where('classes.deleted_at', null)
                ->where('academic_years.is_active', 1)
                ->orderBy('classes.created_at', 'DESC')
                ->get()
                ->getRowArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM VIOLATION] Get class error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get students in class
     * 
     * @param int $classId
     * @return array
     */
    private function getClassStudents($classId)
    {
        try {
            return $this->db->table('students')
                ->select('students.id, students.nisn, students.full_name')
                ->where('students.class_id', $classId)
                ->where('students.deleted_at', null)
                ->orderBy('students.full_name', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM VIOLATION] Get students error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get violations with filters
     * 
     * @param int $classId
     * @param array $filters
     * @return array
     */
    private function getViolations($classId, $filters = [])
    {
        try {
            $builder = $this->db->table('violations')
                ->select('violations.*, 
                         students.full_name as student_name, 
                         students.nisn,
                         violation_categories.category_name,
                         violation_categories.severity,
                         violation_categories.points,
                         users.full_name as reported_by_name')
                ->join('students', 'students.id = violations.student_id')
                ->join('violation_categories', 'violation_categories.id = violations.category_id')
                ->join('users', 'users.id = violations.reported_by')
                ->where('students.class_id', $classId)
                ->where('violations.deleted_at', null);

            // Apply filters
            if (!empty($filters['student_id'])) {
                $builder->where('violations.student_id', $filters['student_id']);
            }

            if (!empty($filters['category_id'])) {
                $builder->where('violations.category_id', $filters['category_id']);
            }

            if (!empty($filters['severity'])) {
                $builder->where('violation_categories.severity', $filters['severity']);
            }

            if (!empty($filters['start_date'])) {
                $builder->where('violations.violation_date >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('violations.violation_date <=', $filters['end_date']);
            }

            if (!empty($filters['status'])) {
                $builder->where('violations.status', $filters['status']);
            }

            if (!empty($filters['search'])) {
                $builder->groupStart()
                    ->like('students.full_name', $filters['search'])
                    ->orLike('students.nisn', $filters['search'])
                    ->orLike('violation_categories.category_name', $filters['search'])
                    ->orLike('violations.description', $filters['search'])
                    ->groupEnd();
            }

            return $builder->orderBy('violations.violation_date', 'DESC')
                ->orderBy('violations.created_at', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM VIOLATION] Get violations error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get violation detail
     * 
     * @param int $id
     * @return array|null
     */
    private function getViolationDetail($id)
    {
        try {
            return $this->db->table('violations')
                ->select('violations.*, 
                         students.id as student_id,
                         students.full_name as student_name, 
                         students.nisn,
                         students.class_id,
                         violation_categories.category_name,
                         violation_categories.severity,
                         violation_categories.points,
                         violation_categories.description as category_description,
                         users.full_name as reported_by_name,
                         handled.full_name as handled_by_name')
                ->join('students', 'students.id = violations.student_id')
                ->join('violation_categories', 'violation_categories.id = violations.category_id')
                ->join('users', 'users.id = violations.reported_by')
                ->join('users as handled', 'handled.id = violations.handled_by', 'left')
                ->where('violations.id', $id)
                ->where('violations.deleted_at', null)
                ->get()
                ->getRowArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM VIOLATION] Get detail error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get sanctions for violation
     * 
     * @param int $violationId
     * @return array
     */
    private function getViolationSanctions($violationId)
    {
        try {
            return $this->db->table('sanctions')
                ->select('sanctions.*, 
                         users.full_name as assigned_by_name,
                         verified.full_name as verified_by_name')
                ->join('users', 'users.id = sanctions.assigned_by')
                ->join('users as verified', 'verified.id = sanctions.verified_by', 'left')
                ->where('sanctions.violation_id', $violationId)
                ->where('sanctions.deleted_at', null)
                ->orderBy('sanctions.sanction_date', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM VIOLATION] Get sanctions error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get student's violation history
     * 
     * @param int $studentId
     * @param int $limit
     * @return array
     */
    private function getStudentViolationHistory($studentId, $limit = 5)
    {
        try {
            return $this->db->table('violations')
                ->select('violations.*, 
                         violation_categories.category_name,
                         violation_categories.severity,
                         violation_categories.points')
                ->join('violation_categories', 'violation_categories.id = violations.category_id')
                ->where('violations.student_id', $studentId)
                ->where('violations.deleted_at', null)
                ->orderBy('violations.violation_date', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM VIOLATION] Get history error: ' . $e->getMessage());
            return [];
        }
    }
}
