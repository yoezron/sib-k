<?php

/**
 * File Path: app/Controllers/HomeroomTeacher/DashboardController.php
 * 
 * Homeroom Teacher Dashboard Controller
 * Menampilkan dashboard untuk Wali Kelas dengan statistik kelas yang diampu
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
use App\Models\CounselingSessionModel;

class DashboardController extends BaseController
{
    protected $classModel;
    protected $studentModel;
    protected $violationModel;
    protected $sessionModel;
    protected $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->studentModel = new StudentModel();
        $this->violationModel = new ViolationModel();
        $this->sessionModel = new CounselingSessionModel();
        $this->db = \Config\Database::connect();

        // Load helpers
        helper(['permission', 'date', 'response']);
    }

    /**
     * Display homeroom teacher dashboard
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
            $data = [
                'title' => 'Dashboard Wali Kelas',
                'pageTitle' => 'Dashboard',
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'url' => '#', 'active' => true],
                ],
                'hasClass' => false,
                'message' => 'Anda belum ditugaskan sebagai wali kelas. Silakan hubungi administrator.',
            ];

            return view('homeroom_teacher/dashboard', $data);
        }

        // Get dashboard statistics
        $stats = $this->getClassStatistics($class['id']);

        // Get recent violations (last 7 days)
        $recentViolations = $this->getRecentViolations($class['id'], 7);

        // Get violation trends (last 6 months)
        $violationTrends = $this->getViolationTrends($class['id'], 6);

        // Get top violators (top 5)
        $topViolators = $this->getTopViolators($class['id'], 5);

        // Get recent counseling sessions for students in this class
        $recentSessions = $this->getRecentSessions($class['id'], 5);

        // Get violation by category
        $violationByCategory = $this->getViolationByCategory($class['id']);

        // Prepare data for view
        $data = [
            'title' => 'Dashboard Wali Kelas',
            'pageTitle' => 'Dashboard',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => '#', 'active' => true],
            ],
            'hasClass' => true,
            'class' => $class,
            'stats' => $stats,
            'recentViolations' => $recentViolations,
            'violationTrends' => $violationTrends,
            'topViolators' => $topViolators,
            'recentSessions' => $recentSessions,
            'violationByCategory' => $violationByCategory,
            'currentUser' => current_user(),
        ];

        return view('homeroom_teacher/dashboard', $data);
    }

    /**
     * Get statistics via AJAX
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getStats()
    {
        // Check authentication
        if (!is_logged_in() || !is_homeroom_teacher()) {
            return json_unauthorized('Unauthorized access');
        }

        $userId = current_user_id();
        $class = $this->getHomeroomClass($userId);

        if (!$class) {
            return json_error('Class not found');
        }

        $stats = $this->getClassStatistics($class['id']);

        return json_success($stats, 'Statistics retrieved successfully');
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
            $class = $this->db->table('classes')
                ->select('classes.*, academic_years.year_name, academic_years.semester')
                ->join('academic_years', 'academic_years.id = classes.academic_year_id')
                ->where('classes.homeroom_teacher_id', $userId)
                ->where('classes.deleted_at', null)
                ->where('academic_years.is_active', 1)
                ->orderBy('classes.created_at', 'DESC')
                ->get()
                ->getRowArray();

            return $class;
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM DASHBOARD] Get class error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get class statistics
     * 
     * @param int $classId
     * @return array
     */
    private function getClassStatistics($classId)
    {
        try {
            $stats = [];

            // Total students
            $stats['total_students'] = $this->db->table('students')
                ->where('class_id', $classId)
                ->where('deleted_at', null)
                ->countAllResults();

            // Total violations this month
            $stats['violations_this_month'] = $this->db->table('violations')
                ->join('students', 'students.id = violations.student_id')
                ->where('students.class_id', $classId)
                ->where('MONTH(violations.violation_date)', date('m'))
                ->where('YEAR(violations.violation_date)', date('Y'))
                ->where('violations.deleted_at', null)
                ->countAllResults();

            // Total violations this week
            $stats['violations_this_week'] = $this->db->table('violations')
                ->join('students', 'students.id = violations.student_id')
                ->where('students.class_id', $classId)
                ->where('violations.violation_date >=', date('Y-m-d', strtotime('-7 days')))
                ->where('violations.deleted_at', null)
                ->countAllResults();

            // Students with violations this month
            $stats['students_with_violations'] = $this->db->table('violations')
                ->select('COUNT(DISTINCT violations.student_id) as count')
                ->join('students', 'students.id = violations.student_id')
                ->where('students.class_id', $classId)
                ->where('MONTH(violations.violation_date)', date('m'))
                ->where('YEAR(violations.violation_date)', date('Y'))
                ->where('violations.deleted_at', null)
                ->get()
                ->getRow()
                ->count ?? 0;

            // Students in counseling this month
            $stats['students_in_counseling'] = $this->db->table('counseling_sessions')
                ->select('COUNT(DISTINCT counseling_sessions.student_id) as count')
                ->join('students', 'students.id = counseling_sessions.student_id')
                ->where('students.class_id', $classId)
                ->where('MONTH(counseling_sessions.session_date)', date('m'))
                ->where('YEAR(counseling_sessions.session_date)', date('Y'))
                ->where('counseling_sessions.deleted_at', null)
                ->get()
                ->getRow()
                ->count ?? 0;

            // Average violation points
            $avgPoints = $this->db->table('violations')
                ->select('AVG(violation_categories.point_deduction) as avg_points')
                ->join('students', 'students.id = violations.student_id')
                ->join('violation_categories', 'violation_categories.id = violations.category_id')
                ->where('students.class_id', $classId)
                ->where('violations.deleted_at', null)
                ->get()
                ->getRow();

            $stats['avg_violation_points'] = $avgPoints ? round($avgPoints->avg_points, 1) : 0;

            // Gender distribution
            $maleCount = $this->db->table('students')
                ->where('class_id', $classId)
                ->where('gender', 'Laki-laki')
                ->where('deleted_at', null)
                ->countAllResults();

            $femaleCount = $this->db->table('students')
                ->where('class_id', $classId)
                ->where('gender', 'Perempuan')
                ->where('deleted_at', null)
                ->countAllResults();

            $stats['gender_distribution'] = [
                'male' => $maleCount,
                'female' => $femaleCount,
            ];

            // Percentage changes (compare with last month)
            $lastMonthViolations = $this->db->table('violations')
                ->join('students', 'students.id = violations.student_id')
                ->where('students.class_id', $classId)
                ->where('MONTH(violations.violation_date)', date('m', strtotime('-1 month')))
                ->where('YEAR(violations.violation_date)', date('Y', strtotime('-1 month')))
                ->where('violations.deleted_at', null)
                ->countAllResults();

            if ($lastMonthViolations > 0) {
                $percentageChange = (($stats['violations_this_month'] - $lastMonthViolations) / $lastMonthViolations) * 100;
                $stats['violation_change_percentage'] = round($percentageChange, 1);
                $stats['violation_trend'] = $percentageChange > 0 ? 'up' : 'down';
            } else {
                $stats['violation_change_percentage'] = 0;
                $stats['violation_trend'] = 'stable';
            }

            return $stats;
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM DASHBOARD] Get statistics error: ' . $e->getMessage());
            return [
                'total_students' => 0,
                'violations_this_month' => 0,
                'violations_this_week' => 0,
                'students_with_violations' => 0,
                'students_in_counseling' => 0,
                'avg_violation_points' => 0,
                'gender_distribution' => ['male' => 0, 'female' => 0],
                'violation_change_percentage' => 0,
                'violation_trend' => 'stable',
            ];
        }
    }

    /**
     * Get recent violations
     * 
     * @param int $classId
     * @param int $days
     * @return array
     */
    private function getRecentViolations($classId, $days = 7)
    {
        try {
            return $this->db->table('violations')
                ->select('violations.*, students.full_name as student_name, students.nisn,
                         violation_categories.category_name, violation_categories.severity_level,
                         violation_categories.point_deduction,
                         users.full_name as reported_by_name')
                ->join('students', 'students.id = violations.student_id')
                ->join('violation_categories', 'violation_categories.id = violations.category_id')
                ->join('users', 'users.id = violations.reported_by')
                ->where('students.class_id', $classId)
                ->where('violations.violation_date >=', date('Y-m-d', strtotime("-{$days} days")))
                ->where('violations.deleted_at', null)
                ->orderBy('violations.violation_date', 'DESC')
                ->orderBy('violations.created_at', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM DASHBOARD] Get recent violations error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get violation trends (monthly data for charts)
     * 
     * @param int $classId
     * @param int $months
     * @return array
     */
    private function getViolationTrends($classId, $months = 6)
    {
        try {
            $trends = [];

            for ($i = $months - 1; $i >= 0; $i--) {
                $month = date('Y-m', strtotime("-{$i} months"));
                $monthName = date('M Y', strtotime("-{$i} months"));

                $count = $this->db->table('violations')
                    ->join('students', 'students.id = violations.student_id')
                    ->where('students.class_id', $classId)
                    ->where("DATE_FORMAT(violations.violation_date, '%Y-%m')", $month)
                    ->where('violations.deleted_at', null)
                    ->countAllResults();

                $trends[] = [
                    'month' => $monthName,
                    'count' => $count,
                ];
            }

            return $trends;
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM DASHBOARD] Get violation trends error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get top violators
     * 
     * @param int $classId
     * @param int $limit
     * @return array
     */
    private function getTopViolators($classId, $limit = 5)
    {
        try {
            return $this->db->table('students')
                ->select('students.id, students.full_name, students.nisn, 
                         COUNT(violations.id) as violation_count,
                        SUM(violation_categories.point_deduction) as total_points')
                ->join('violations', 'violations.student_id = students.id AND violations.deleted_at IS NULL', 'left')
                ->join('violation_categories', 'violation_categories.id = violations.category_id', 'left')
                ->where('students.class_id', $classId)
                ->where('students.deleted_at', null)
                ->groupBy('students.id')
                ->having('violation_count >', 0)
                ->orderBy('total_points', 'DESC')
                ->orderBy('violation_count', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM DASHBOARD] Get top violators error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent counseling sessions
     * 
     * @param int $classId
     * @param int $limit
     * @return array
     */
    private function getRecentSessions($classId, $limit = 5)
    {
        try {
            return $this->db->table('counseling_sessions')
                ->select('counseling_sessions.*, students.full_name as student_name, students.nisn,
                         users.full_name as counselor_name')
                ->join('students', 'students.id = counseling_sessions.student_id')
                ->join('users', 'users.id = counseling_sessions.counselor_id')
                ->where('students.class_id', $classId)
                ->where('counseling_sessions.deleted_at', null)
                ->orderBy('counseling_sessions.session_date', 'DESC')
                ->orderBy('counseling_sessions.created_at', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM DASHBOARD] Get recent sessions error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get violations grouped by category
     * 
     * @param int $classId
     * @return array
     */
    private function getViolationByCategory($classId)
    {
        try {
            return $this->db->table('violation_categories')
                ->select('violation_categories.category_name,
                         COUNT(violations.id) as count,
                         violation_categories.severity_level')
                ->join('violations', 'violations.category_id = violation_categories.id AND violations.deleted_at IS NULL', 'left')
                ->join('students', 'students.id = violations.student_id', 'left')
                ->where('students.class_id', $classId)
                ->where('violation_categories.deleted_at', null)
                ->groupBy('violation_categories.id')
                ->orderBy('count', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM DASHBOARD] Get violation by category error: ' . $e->getMessage());
            return [];
        }
    }
}
