<?php

/**
 * File Path: app/Controllers/HomeroomTeacher/DashboardController.php
 * 
 * FINAL VERSION - 100% SESUAI DENGAN DATABASE STRUCTURE
 * 
 * Database Columns Confirmed:
 * - students.full_name ✅ EXISTS
 * - students.total_violation_points ✅ EXISTS (bukan violation_points!)
 * - users.full_name ✅ EXISTS
 * - violation_categories.points ✅ EXISTS
 * 
 * @package    SIB-K
 * @version    EXACT-MATCH-1.0
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
    protected $db;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->violationModel = new ViolationModel();
        $this->sessionModel = new CounselingSessionModel();
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_wali_kelas()) {
            return redirect()->to('/')->with('error', 'Akses ditolak. Halaman ini hanya untuk Wali Kelas.');
        }

        $userId = auth_id();
        $homeroomClass = $this->getHomeroomClass($userId);

        if (!$homeroomClass) {
            return view('homeroom_teacher/no_class', [
                'title' => 'Dashboard Wali Kelas',
                'pageTitle' => 'Tidak Ada Kelas',
                'message' => 'Anda belum ditugaskan sebagai wali kelas untuk tahun ajaran aktif.',
            ]);
        }

        $classId = $homeroomClass['id'];

        $data = [
            'title' => 'Dashboard Wali Kelas',
            'pageTitle' => 'Dashboard - ' . $homeroomClass['class_name'],
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => '#', 'active' => true],
            ],
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

    public function getStats()
    {
        if (!is_logged_in() || !is_wali_kelas()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
        }

        $userId = auth_id();
        $homeroomClass = $this->getHomeroomClass($userId);

        if (!$homeroomClass) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kelas tidak ditemukan'
            ]);
        }

        $statistics = $this->getClassStatistics($homeroomClass['id']);

        return $this->response->setJSON([
            'success' => true,
            'data' => $statistics
        ]);
    }

    private function getHomeroomClass($userId)
    {
        return $this->classModel
            ->select('classes.*, 
                      academic_years.year_name, 
                      academic_years.semester,
                      academic_years.start_date,
                      academic_years.end_date,
                      academic_years.is_active as year_active')
            ->join('academic_years', 'academic_years.id = classes.academic_year_id')
            ->where('classes.homeroom_teacher_id', $userId)
            ->where('classes.is_active', 1)
            ->where('academic_years.is_active', 1)
            ->first();
    }

    /**
     * ✅ USING CORRECT COLUMN NAME: total_violation_points
     */
    private function getClassStatistics($classId)
    {
        // Total students
        $totalStudents = $this->db->table('students')
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->where('deleted_at', null)
            ->countAllResults();

        // Violations this month
        $thisMonth = date('Y-m-01');
        $violationsThisMonth = $this->db->table('violations')
            ->join('students', 'students.id = violations.student_id', 'inner')
            ->where('students.class_id', $classId)
            ->where('violations.violation_date >=', $thisMonth)
            ->where('violations.deleted_at', null)
            ->countAllResults();

        // Students with violations
        $studentsWithViolations = $this->db->table('violations')
            ->select('COUNT(DISTINCT violations.student_id) as count')
            ->join('students', 'students.id = violations.student_id', 'inner')
            ->where('students.class_id', $classId)
            ->where('violations.deleted_at', null)
            ->get()
            ->getRowArray();

        // Counseling sessions this month
        $sessionsThisMonth = $this->db->table('counseling_sessions')
            ->join('students', 'students.id = counseling_sessions.student_id', 'inner')
            ->where('students.class_id', $classId)
            ->where('counseling_sessions.session_date >=', $thisMonth)
            ->where('counseling_sessions.deleted_at', null)
            ->countAllResults();

        // ✅ FIXED: Use students.total_violation_points (column yang benar!)
        $totalPoints = $this->db->table('students')
            ->select('SUM(total_violation_points) as total_points')
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        $avgPoints = $totalStudents > 0 ?
            round(($totalPoints['total_points'] ?? 0) / $totalStudents, 1) : 0;

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
     * ✅ USING students.full_name (column exists!)
     */
    private function getRecentViolations($classId, $limit = 5)
    {
        $query = "
            SELECT 
                v.*,
                s.full_name as student_name,
                s.nisn,
                vc.category_name,
                vc.points,
                vc.severity_level,
                u.full_name as reported_by_name
            FROM violations v
            INNER JOIN students s ON s.id = v.student_id
            INNER JOIN violation_categories vc ON vc.id = v.category_id
            LEFT JOIN users u ON u.id = v.reported_by
            WHERE s.class_id = ?
              AND v.deleted_at IS NULL
            ORDER BY v.violation_date DESC, v.created_at DESC
            LIMIT ?
        ";

        return $this->db->query($query, [$classId, $limit])->getResultArray();
    }

    private function getTopViolations($classId, $limit = 5)
    {
        $query = "
            SELECT 
                vc.category_name,
                vc.severity_level,
                COUNT(*) as violation_count,
                SUM(vc.points) as total_points
            FROM violations v
            INNER JOIN students s ON s.id = v.student_id
            INNER JOIN violation_categories vc ON vc.id = v.category_id
            WHERE s.class_id = ?
              AND v.deleted_at IS NULL
            GROUP BY v.category_id
            ORDER BY violation_count DESC
            LIMIT ?
        ";

        return $this->db->query($query, [$classId, $limit])->getResultArray();
    }

    /**
     * ✅ USING students.full_name
     */
    private function getUpcomingSessions($classId, $limit = 5)
    {
        $today = date('Y-m-d');

        $query = "
            SELECT 
                cs.*,
                s.full_name as student_name,
                s.nisn,
                u.full_name as counselor_name
            FROM counseling_sessions cs
            INNER JOIN students s ON s.id = cs.student_id
            LEFT JOIN users u ON u.id = cs.counselor_id
            WHERE s.class_id = ?
              AND cs.session_date >= ?
              AND cs.status = 'Terjadwal'
              AND cs.deleted_at IS NULL
            ORDER BY cs.session_date ASC
            LIMIT ?
        ";

        return $this->db->query($query, [$classId, $today, $limit])->getResultArray();
    }

    /**
     * ✅ USING students.total_violation_points
     */
    private function getStudentsNeedAttention($classId, $limit = 5)
    {
        $query = "
            SELECT 
                s.id,
                s.nisn,
                s.full_name,
                s.gender,
                s.status,
                s.total_violation_points as violation_points,
                COUNT(v.id) as violation_count
            FROM students s
            LEFT JOIN violations v ON v.student_id = s.id AND v.deleted_at IS NULL
            WHERE s.class_id = ?
              AND s.status = 'Aktif'
              AND s.deleted_at IS NULL
            GROUP BY s.id
            HAVING s.total_violation_points > 0
            ORDER BY s.total_violation_points DESC
            LIMIT ?
        ";

        return $this->db->query($query, [$classId, $limit])->getResultArray();
    }

    private function getMonthlyViolationTrend($classId, $months = 6)
    {
        $dateFrom = date('Y-m-01', strtotime("-{$months} months"));

        $query = "
            SELECT 
                DATE_FORMAT(v.violation_date, '%Y-%m') as month,
                COUNT(*) as violation_count
            FROM violations v
            INNER JOIN students s ON s.id = v.student_id
            WHERE s.class_id = ?
              AND v.violation_date >= ?
              AND v.deleted_at IS NULL
            GROUP BY month
            ORDER BY month ASC
        ";

        return $this->db->query($query, [$classId, $dateFrom])->getResultArray();
    }
}
