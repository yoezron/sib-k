<?php

/**
 * File Path: app/Controllers/HomeroomTeacher/ViolationController.php
 * 
 * Homeroom Teacher Violation Controller
 * Controller untuk wali kelas mengelola pelanggaran siswa
 * 
 * @package    SIB-K
 * @subpackage Controllers
 * @category   Controller
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Controllers\HomeroomTeacher;

use App\Controllers\BaseController;
use App\Models\ViolationModel;
use App\Models\ViolationCategoryModel;
use App\Models\StudentModel;
use App\Models\ClassModel;
use App\Models\UserModel;

class ViolationController extends BaseController
{
    protected $violationModel;
    protected $categoryModel;
    protected $studentModel;
    protected $classModel;
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->violationModel = new ViolationModel();
        $this->categoryModel = new ViolationCategoryModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->userModel = new UserModel();
        $this->session = session();
    }

    /**
     * Display violations list
     * 
     * @return string
     */
    public function index()
    {
        // Check authentication
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check role
        if (session()->get('role_name') !== 'WALI_KELAS') {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $userId = session()->get('user_id');

        // Get homeroom class
        $homeroomClass = $this->getHomeroomClass($userId);

        if (!$homeroomClass) {
            return view('homeroom_teacher/no_class', [
                'title' => 'Daftar Pelanggaran',
            ]);
        }

        $classId = $homeroomClass['id'];

        // Get filters
        $filters = [
            'class_id' => $classId,
            'status' => $this->request->getGet('status'),
            'severity_level' => $this->request->getGet('severity_level'),
            'category_id' => $this->request->getGet('category_id'),
            'student_id' => $this->request->getGet('student_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search'),
        ];

        // Get violations
        $violations = $this->getViolations($filters);

        // Get filter options
        $data = [
            'title' => 'Daftar Pelanggaran',
            'homeroom_class' => $homeroomClass,
            'violations' => $violations,
            'students' => $this->getClassStudents($classId),
            'categories' => $this->categoryModel->where('is_active', 1)->findAll(),
            'filters' => $filters,
            'statistics' => $this->getViolationStatistics($classId),
        ];

        return view('homeroom_teacher/violations/index', $data);
    }

    /**
     * Show create violation form
     * 
     * @return string
     */
    public function create()
    {
        // Check authentication
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check role
        if (session()->get('role_name') !== 'WALI_KELAS') {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $userId = session()->get('user_id');

        // Get homeroom class
        $homeroomClass = $this->getHomeroomClass($userId);

        if (!$homeroomClass) {
            return redirect()->to('homeroom-teacher/dashboard')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas');
        }

        $classId = $homeroomClass['id'];

        $data = [
            'title' => 'Catat Pelanggaran Siswa',
            'homeroom_class' => $homeroomClass,
            'students' => $this->getClassStudents($classId),
            'categories' => $this->categoryModel->where('is_active', 1)->orderBy('category_name')->findAll(),
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
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check role
        if (session()->get('role_name') !== 'WALI_KELAS') {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $userId = session()->get('user_id');

        // Validate input
        $rules = [
            'student_id' => 'required|integer',
            'category_id' => 'required|integer',
            'violation_date' => 'required|valid_date',
            'description' => 'required|min_length[10]',
            'location' => 'permit_empty|max_length[200]',
            'witness' => 'permit_empty|max_length[200]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Get homeroom class
        $homeroomClass = $this->getHomeroomClass($userId);

        if (!$homeroomClass) {
            return redirect()->to('homeroom-teacher/dashboard')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas');
        }

        // Validate student belongs to this class
        $studentId = $this->request->getPost('student_id');
        $student = $this->studentModel->find($studentId);

        if (!$student || $student['class_id'] != $homeroomClass['id']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Siswa tidak terdaftar di kelas Anda');
        }

        // Prepare data
        $data = [
            'student_id' => $studentId,
            'category_id' => $this->request->getPost('category_id'),
            'violation_date' => $this->request->getPost('violation_date'),
            'description' => $this->request->getPost('description'),
            'location' => $this->request->getPost('location'),
            'witness' => $this->request->getPost('witness'),
            'reported_by' => $userId,
            'status' => 'Dilaporkan',
            'is_repeat_offender' => $this->checkRepeatOffender($studentId, $this->request->getPost('category_id')),
        ];

        try {
            $violationId = $this->violationModel->insert($data);

            if (!$violationId) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->violationModel->errors());
            }

            // Check if parent notification is needed
            $category = $this->categoryModel->find($data['category_id']);
            $notifyParent = ($category['severity_level'] === 'Berat' || $data['is_repeat_offender']);

            if ($notifyParent) {
                // Update parent notification flag
                $this->violationModel->update($violationId, ['parent_notified' => 1]);

                // TODO: Send notification to parent (implement later in notification module)
            }

            return redirect()->to('homeroom-teacher/violations')
                ->with('success', 'Pelanggaran berhasil dicatat. ' .
                    ($notifyParent ? 'Notifikasi telah dikirim ke orang tua.' : ''));
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
    public function show($id)
    {
        // Check authentication
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check role
        if (session()->get('role_name') !== 'WALI_KELAS') {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $userId = session()->get('user_id');
        $homeroomClass = $this->getHomeroomClass($userId);

        if (!$homeroomClass) {
            return redirect()->to('homeroom-teacher/dashboard')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas');
        }

        // Get violation with details
        $violation = $this->violationModel
            ->select('violations.*, 
                      students.full_name as student_name,
                      students.nisn,
                      students.gender,
                      classes.class_name,
                      violation_categories.category_name,
                      violation_categories.severity_level,
                      violation_categories.points,
                      reported_by.full_name as reported_by_name,
                      handled_by.full_name as handled_by_name')
            ->join('students', 'students.id = violations.student_id')
            ->join('classes', 'classes.id = students.class_id')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->join('users as reported_by', 'reported_by.id = violations.reported_by', 'left')
            ->join('users as handled_by', 'handled_by.id = violations.handled_by', 'left')
            ->where('violations.id', $id)
            ->first();

        if (!$violation) {
            return redirect()->to('homeroom-teacher/violations')
                ->with('error', 'Data pelanggaran tidak ditemukan');
        }

        // Check if violation belongs to homeroom class
        if ($violation['class_name'] !== $homeroomClass['class_name']) {
            return redirect()->to('homeroom-teacher/violations')
                ->with('error', 'Anda tidak memiliki akses ke data ini');
        }

        // Get sanctions
        $db = \Config\Database::connect();
        $sanctions = $db->table('sanctions')
            ->select('sanctions.*, users.full_name as assigned_by_name')
            ->join('users', 'users.id = sanctions.assigned_by', 'left')
            ->where('violation_id', $id)
            ->where('sanctions.deleted_at', null)
            ->orderBy('sanctions.sanction_date', 'DESC')
            ->get()
            ->getResultArray();

        // Get student violation history
        $violationHistory = $this->violationModel
            ->select('violations.*, violation_categories.category_name, violation_categories.points')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('violations.student_id', $violation['student_id'])
            ->where('violations.id !=', $id)
            ->orderBy('violations.violation_date', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'title' => 'Detail Pelanggaran',
            'homeroom_class' => $homeroomClass,
            'violation' => $violation,
            'sanctions' => $sanctions,
            'violation_history' => $violationHistory,
        ];

        return view('homeroom_teacher/violations/detail', $data);
    }

    /**
     * Get homeroom class
     * 
     * @param int $userId
     * @return array|null
     */
    private function getHomeroomClass($userId)
    {
        return $this->classModel
            ->select('classes.*, academic_years.year_name, academic_years.semester')
            ->join('academic_years', 'academic_years.id = classes.academic_year_id', 'left')
            ->where('classes.homeroom_teacher_id', $userId)
            ->where('academic_years.is_active', 1)
            ->first();
    }

    /**
     * Get class students
     * 
     * @param int $classId
     * @return array
     */
    private function getClassStudents($classId)
    {
        return $this->studentModel
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->orderBy('full_name', 'ASC')
            ->findAll();
    }

    /**
     * Get violations with filters
     * 
     * @param array $filters
     * @return array
     */
    private function getViolations($filters)
    {
        $builder = $this->violationModel
            ->select('violations.*, 
                      students.full_name as student_name,
                      students.nisn,
                      violation_categories.category_name,
                      violation_categories.severity_level,
                      violation_categories.points,
                      reported_by.full_name as reported_by_name')
            ->join('students', 'students.id = violations.student_id')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->join('users as reported_by', 'reported_by.id = violations.reported_by', 'left')
            ->where('students.class_id', $filters['class_id']);

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('violations.status', $filters['status']);
        }

        if (!empty($filters['severity_level'])) {
            $builder->where('violation_categories.severity_level', $filters['severity_level']);
        }

        if (!empty($filters['category_id'])) {
            $builder->where('violations.category_id', $filters['category_id']);
        }

        if (!empty($filters['student_id'])) {
            $builder->where('violations.student_id', $filters['student_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('violations.violation_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('violations.violation_date <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('students.full_name', $filters['search'])
                ->orLike('students.nisn', $filters['search'])
                ->orLike('violation_categories.category_name', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('violations.violation_date', 'DESC')
            ->orderBy('violations.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get violation statistics
     * 
     * @param int $classId
     * @return array
     */
    private function getViolationStatistics($classId)
    {
        $db = \Config\Database::connect();

        // Total violations
        $total = $db->table('violations')
            ->join('students', 'students.id = violations.student_id')
            ->where('students.class_id', $classId)
            ->where('violations.deleted_at', null)
            ->countAllResults();

        // This month
        $thisMonth = $db->table('violations')
            ->join('students', 'students.id = violations.student_id')
            ->where('students.class_id', $classId)
            ->where('violations.violation_date >=', date('Y-m-01'))
            ->where('violations.deleted_at', null)
            ->countAllResults();

        // By severity
        $bySeverity = $db->table('violations')
            ->select('violation_categories.severity_level, COUNT(*) as count')
            ->join('students', 'students.id = violations.student_id')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('students.class_id', $classId)
            ->where('violations.deleted_at', null)
            ->groupBy('violation_categories.severity_level')
            ->get()
            ->getResultArray();

        $severity = [
            'Ringan' => 0,
            'Sedang' => 0,
            'Berat' => 0,
        ];

        foreach ($bySeverity as $row) {
            $severity[$row['severity_level']] = (int) $row['count'];
        }

        return [
            'total' => $total,
            'this_month' => $thisMonth,
            'severity' => $severity,
        ];
    }

    /**
     * Check if student is repeat offender
     * 
     * @param int $studentId
     * @param int $categoryId
     * @return int
     */
    private function checkRepeatOffender($studentId, $categoryId)
    {
        $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));

        $count = $this->violationModel
            ->where('student_id', $studentId)
            ->where('category_id', $categoryId)
            ->where('violation_date >=', $threeMonthsAgo)
            ->countAllResults();

        return $count > 0 ? 1 : 0;
    }
}
