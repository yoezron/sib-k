<?php

/**
 * File Path: app/Controllers/HomeroomTeacher/DashboardController.php
 * 
 * Homeroom Teacher Dashboard Controller
 * Controller untuk dashboard wali kelas
 * 
 * @package    SIB-K
 * @subpackage Controllers
 * @category   Controller
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Controllers\HomeroomTeacher;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\ClassModel;
use App\Models\ViolationModel;
use App\Models\CounselingSessionModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    protected $studentModel;
    protected $classModel;
    protected $violationModel;
    protected $sessionModel;
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->violationModel = new ViolationModel();
        $this->sessionModel = new CounselingSessionModel();
        $this->userModel = new UserModel();
        $this->session = session();
    }

    /**
     * Display homeroom teacher dashboard
     * 
     * @return string
     */
    public function index()
    {
        // Check authentication
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check role - only homeroom teacher
        $userRole = session()->get('role_name');
        if ($userRole !== 'WALI_KELAS') {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $userId = session()->get('user_id');

        // Get homeroom teacher's class
        $homeroomClass = $this->getHomeroomClass($userId);

        if (!$homeroomClass) {
            return view('homeroom_teacher/no_class', [
                'title' => 'Dashboard Wali Kelas',
            ]);
        }

        $classId = $homeroomClass['id'];

        // Get statistics
        $data = [
            'title' => 'Dashboard Wali Kelas',
            'homeroom_class' => $homeroomClass,
            'statistics' => $this->getClassStatistics($classId),
            'recent_violations' => $this->getRecentViolations($classId, 5),
            'top_violations' => $this->getTopViolations($classId, 5),
            'upcoming_sessions' => $this->getUpcomingSessions($classId, 5),
            'students_need_attention' => $this->getStudentsNeedAttention($classId, 5),
            'monthly_violation_trend' => $this->getMonthlyViolationTrend($classId, 6),
        ];

        return view('homeroom_teacher/dashboard', $data);
    }

    /**
     * Get homeroom class for teacher
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
     * Get class statistics
     * 
     * @param int $classId
     * @return array
     */
    private function getClassStatistics($classId)
    {
        $db = \Config\Database::connect();

        // Total students
        $totalStudents = $this->studentModel
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->countAllResults();

        // Total violations this month
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        $violationsThisMonth = $db->table('violations')
            ->join('students', 'students.id = violations.student_id')
            ->where('students.class_id', $classId)
            ->where('violations.violation_date >=', $startOfMonth)
            ->where('violations.violation_date <=', $endOfMonth)
            ->where('violations.deleted_at', null)
            ->countAllResults();

        // Students with violations
        $studentsWithViolations = $db->table('violations')
            ->select('COUNT(DISTINCT student_id) as count')
            ->join('students', 'students.id = violations.student_id')
            ->where('students.class_id', $classId)
            ->where('violations.violation_date >=', $startOfMonth)
            ->where('violations.violation_date <=', $endOfMonth)
            ->where('violations.deleted_at', null)
            ->get()
            ->getRowArray();

        // Counseling sessions this month
        $sessionsThisMonth = $db->table('counseling_sessions')
            ->where('class_id', $classId)
            ->where('session_date >=', $startOfMonth)
            ->where('session_date <=', $endOfMonth)
            ->where('deleted_at', null)
            ->countAllResults();

        // Average violation points per student
        $totalPoints = $db->table('violations')
            ->selectSum('violation_categories.points', 'total_points')
            ->join('students', 'students.id = violations.student_id')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('students.class_id', $classId)
            ->where('violations.deleted_at', null)
            ->get()
            ->getRowArray();

        $avgPoints = $totalStudents > 0 ? round(($totalPoints['total_points'] ?? 0) / $totalStudents, 1) : 0;

        return [
            'total_students' => $totalStudents,
            'violations_this_month' => $violationsThisMonth,
            'students_with_violations' => $studentsWithViolations['count'] ?? 0,
            'counseling_sessions' => $sessionsThisMonth,
            'average_violation_points' => $avgPoints,
            'violation_free_students' => $totalStudents - ($studentsWithViolations['count'] ?? 0),
        ];
    }

    /**
     * Get recent violations
     * 
     * @param int $classId
     * @param int $limit
     * @return array
     */
    private function getRecentViolations($classId, $limit = 5)
    {
        return $this->violationModel
            ->select('violations.*, 
                      students.full_name as student_name,
                      students.nisn,
                      violation_categories.category_name,
                      violation_categories.points,
                      violation_categories.severity_level,
                      users.full_name as reported_by_name')
            ->join('students', 'students.id = violations.student_id')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->join('users', 'users.id = violations.reported_by', 'left')
            ->where('students.class_id', $classId)
            ->orderBy('violations.violation_date', 'DESC')
            ->orderBy('violations.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get top violations in class
     * 
     * @param int $classId
     * @param int $limit
     * @return array
     */
    private function getTopViolations($classId, $limit = 5)
    {
        $db = \Config\Database::connect();

        return $db->table('violations')
            ->select('violation_categories.category_name,
                      violation_categories.severity_level,
                      COUNT(*) as violation_count,
                      SUM(violation_categories.points) as total_points')
            ->join('students', 'students.id = violations.student_id')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('students.class_id', $classId)
            ->where('violations.deleted_at', null)
            ->groupBy('violations.category_id')
            ->orderBy('violation_count', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get upcoming counseling sessions
     * 
     * @param int $classId
     * @param int $limit
     * @return array
     */
    private function getUpcomingSessions($classId, $limit = 5)
    {
        $today = date('Y-m-d');

        return $this->sessionModel
            ->select('counseling_sessions.*,
                      students.full_name as student_name,
                      users.full_name as counselor_name')
            ->join('students', 'students.id = counseling_sessions.student_id', 'left')
            ->join('users', 'users.id = counseling_sessions.counselor_id')
            ->where('counseling_sessions.class_id', $classId)
            ->where('counseling_sessions.session_date >=', $today)
            ->where('counseling_sessions.status', 'DIJADWALKAN')
            ->orderBy('counseling_sessions.session_date', 'ASC')
            ->orderBy('counseling_sessions.session_time', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get students that need attention
     * 
     * @param int $classId
     * @param int $limit
     * @return array
     */
    private function getStudentsNeedAttention($classId, $limit = 5)
    {
        $db = \Config\Database::connect();

        // Get students with high violation points
        return $db->table('students')
            ->select('students.id,
                      students.full_name,
                      students.nisn,
                      COUNT(violations.id) as violation_count,
                      SUM(violation_categories.points) as total_points')
            ->join('violations', 'violations.student_id = students.id', 'left')
            ->join('violation_categories', 'violation_categories.id = violations.category_id', 'left')
            ->where('students.class_id', $classId)
            ->where('students.status', 'Aktif')
            ->where('violations.deleted_at', null)
            ->groupBy('students.id')
            ->having('total_points >', 0)
            ->orderBy('total_points', 'DESC')
            ->orderBy('violation_count', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get monthly violation trend
     * 
     * @param int $classId
     * @param int $months
     * @return array
     */
    private function getMonthlyViolationTrend($classId, $months = 6)
    {
        $db = \Config\Database::connect();
        $trend = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd = date('Y-m-t', strtotime("-$i months"));
            $monthName = date('M Y', strtotime("-$i months"));

            $count = $db->table('violations')
                ->join('students', 'students.id = violations.student_id')
                ->where('students.class_id', $classId)
                ->where('violations.violation_date >=', $monthStart)
                ->where('violations.violation_date <=', $monthEnd)
                ->where('violations.deleted_at', null)
                ->countAllResults();

            $trend[] = [
                'month' => $monthName,
                'count' => $count,
            ];
        }

        return $trend;
    }

    /**
     * Get class report summary (for AJAX)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getClassSummary()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $userId = session()->get('user_id');
        $homeroomClass = $this->getHomeroomClass($userId);

        if (!$homeroomClass) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kelas tidak ditemukan'
            ]);
        }

        $classId = $homeroomClass['id'];

        $summary = [
            'class_info' => $homeroomClass,
            'statistics' => $this->getClassStatistics($classId),
            'top_violations' => $this->getTopViolations($classId, 10),
            'students_need_attention' => $this->getStudentsNeedAttention($classId, 10),
        ];

        return $this->response->setJSON([
            'success' => true,
            'data' => $summary
        ]);
    }
}
