<?php

/**
 * File Path: app/Controllers/Counselor/DashboardController.php
 * 
 * Counselor Dashboard Controller
 * Menampilkan dashboard untuk Guru BK dengan statistik, jadwal, dan data siswa binaan
 * 
 * @package    SIB-K
 * @subpackage Controllers/Counselor
 * @category   Dashboard
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Controllers\Counselor;

use App\Controllers\BaseController;
use App\Services\CounselingService;
use App\Models\CounselingSessionModel;
use App\Models\StudentModel;
use App\Models\ViolationModel;

class DashboardController extends BaseController
{
    protected $counselingService;
    protected $sessionModel;
    protected $studentModel;
    protected $violationModel;
    protected $db;

    public function __construct()
    {
        $this->counselingService = new CounselingService();
        $this->sessionModel = new CounselingSessionModel();
        $this->studentModel = new StudentModel();
        $this->db = \Config\Database::connect();

        // Check if ViolationModel exists, if not we'll handle it gracefully
        if (class_exists('\App\Models\ViolationModel')) {
            $this->violationModel = new ViolationModel();
        }
    }

    /**
     * Display counselor dashboard
     * 
     * @return string
     */
    public function index()
    {
        // Check authentication and role
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $counselorId = auth_id();

        // Get dashboard statistics
        $data['stats'] = $this->counselingService->getDashboardStats($counselorId);

        // Get today's sessions
        $data['todaySessions'] = $this->getTodaySessions($counselorId);

        // Get upcoming sessions (next 7 days)
        $data['upcomingSessions'] = $this->getUpcomingSessions($counselorId);

        // Get assigned students (siswa binaan)
        $data['assignedStudents'] = $this->getAssignedStudents($counselorId);

        // Get chart data for last 6 months
        $data['chartData'] = $this->getSessionChartData($counselorId);

        // Get recent activities
        $data['recentActivities'] = $this->getRecentActivities($counselorId);

        // Get pending sessions (need follow-up)
        $data['pendingSessions'] = $this->getPendingSessions($counselorId);

        // Page metadata
        $data['title'] = 'Dashboard Guru BK';
        $data['pageTitle'] = 'Dashboard';
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => base_url('counselor/dashboard')],
            ['title' => 'Dashboard', 'url' => '#', 'active' => true],
        ];

        return view('counselor/dashboard', $data);
    }

    /**
     * Get today's counseling sessions
     * 
     * @param int $counselorId
     * @return array
     */
    private function getTodaySessions($counselorId)
    {
        $today = date('Y-m-d');

        return $this->sessionModel
            ->select('counseling_sessions.*,
                      students.nisn, students.nis,
                      users.full_name as student_name,
                      classes.class_name')
            ->join('students', 'students.id = counseling_sessions.student_id', 'left')
            ->join('users', 'users.id = students.user_id', 'left')
            ->join('classes', 'classes.id = counseling_sessions.class_id', 'left')
            ->where('counseling_sessions.counselor_id', $counselorId)
            ->where('counseling_sessions.session_date', $today)
            ->where('counseling_sessions.status !=', 'Dibatalkan')
            ->orderBy('counseling_sessions.session_time', 'ASC')
            ->findAll();
    }

    /**
     * Get upcoming counseling sessions (next 7 days)
     * 
     * @param int $counselorId
     * @return array
     */
    private function getUpcomingSessions($counselorId)
    {
        $today = date('Y-m-d');
        $nextWeek = date('Y-m-d', strtotime('+7 days'));

        return $this->sessionModel
            ->select('counseling_sessions.*,
                      students.nisn, students.nis,
                      users.full_name as student_name,
                      classes.class_name')
            ->join('students', 'students.id = counseling_sessions.student_id', 'left')
            ->join('users', 'users.id = students.user_id', 'left')
            ->join('classes', 'classes.id = counseling_sessions.class_id', 'left')
            ->where('counseling_sessions.counselor_id', $counselorId)
            ->where('counseling_sessions.session_date >', $today)
            ->where('counseling_sessions.session_date <=', $nextWeek)
            ->where('counseling_sessions.status', 'Dijadwalkan')
            ->orderBy('counseling_sessions.session_date', 'ASC')
            ->orderBy('counseling_sessions.session_time', 'ASC')
            ->limit(10)
            ->findAll();
    }

    /**
     * Get assigned students (siswa binaan) for counselor
     * This is a simplified version - you may need to adjust based on your business logic
     * 
     * @param int $counselorId
     * @return array
     */
    private function getAssignedStudents($counselorId)
    {
        // Get students who have had sessions with this counselor
        // You might want to add a dedicated assignment table in the future
        $students = $this->db->table('students')
            ->select('students.id, students.nisn, students.nis, students.total_violation_points,
                      users.full_name as student_name, users.email,
                      classes.class_name,
                      COUNT(DISTINCT counseling_sessions.id) as total_sessions')
            ->join('users', 'users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('counseling_sessions', 'counseling_sessions.student_id = students.id 
                    AND counseling_sessions.counselor_id = ' . $counselorId, 'left')
            ->where('students.status', 'Aktif')
            ->groupBy('students.id, students.nisn, students.nis, students.total_violation_points,
                       users.full_name, users.email, classes.class_name')
            ->having('COUNT(DISTINCT counseling_sessions.id) >', 0)
            ->orderBy('total_sessions', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $students;
    }

    /**
     * Get chart data for session statistics (last 6 months)
     * 
     * @param int $counselorId
     * @return array
     */
    private function getSessionChartData($counselorId)
    {
        $chartData = [
            'labels' => [],
            'individual' => [],
            'group' => [],
            'class' => [],
        ];

        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $monthName = date('M Y', strtotime("-{$i} months"));

            $chartData['labels'][] = $monthName;

            // Count by session type
            $individual = $this->sessionModel
                ->where('counselor_id', $counselorId)
                ->where('session_type', 'Individu')
                ->like('session_date', $month)
                ->where('status !=', 'Dibatalkan')
                ->countAllResults(false);

            $group = $this->sessionModel
                ->where('counselor_id', $counselorId)
                ->where('session_type', 'Kelompok')
                ->like('session_date', $month)
                ->where('status !=', 'Dibatalkan')
                ->countAllResults(false);

            $class = $this->sessionModel
                ->where('counselor_id', $counselorId)
                ->where('session_type', 'Klasikal')
                ->like('session_date', $month)
                ->where('status !=', 'Dibatalkan')
                ->countAllResults(false);

            $chartData['individual'][] = $individual;
            $chartData['group'][] = $group;
            $chartData['class'][] = $class;
        }

        return $chartData;
    }

    /**
     * Get recent activities
     * 
     * @param int $counselorId
     * @return array
     */
    private function getRecentActivities($counselorId)
    {
        // Use query builder directly to avoid soft delete conflicts
        $recentSessions = $this->db->table('counseling_sessions')
            ->select('counseling_sessions.id, counseling_sessions.topic, 
                      counseling_sessions.session_date, counseling_sessions.session_type,
                      counseling_sessions.updated_at,
                      students.nisn,
                      users.full_name as student_name')
            ->join('students', 'students.id = counseling_sessions.student_id', 'left')
            ->join('users', 'users.id = students.user_id', 'left')
            ->where('counseling_sessions.counselor_id', $counselorId)
            ->where('counseling_sessions.status', 'Selesai')
            ->where('counseling_sessions.deleted_at', null)
            ->orderBy('counseling_sessions.updated_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $activities = [];

        foreach ($recentSessions as $session) {
            $activities[] = [
                'type' => 'session_completed',
                'icon' => 'mdi-check-circle',
                'color' => 'success',
                'title' => 'Sesi Konseling Selesai',
                'description' => 'Sesi "' . $session['topic'] . '" dengan ' .
                    ($session['student_name'] ?? 'Kelompok/Kelas'),
                'time' => time_ago($session['updated_at']),
                'url' => base_url('counselor/sessions/detail/' . $session['id']),
            ];
        }

        return $activities;
    }

    /**
     * Get pending sessions that need follow-up
     * 
     * @param int $counselorId
     * @return array
     */
    private function getPendingSessions($counselorId)
    {
        // Use query builder directly to avoid soft delete conflicts
        return $this->db->table('counseling_sessions')
            ->select('counseling_sessions.*,
                      students.nisn,
                      users.full_name as student_name')
            ->join('students', 'students.id = counseling_sessions.student_id', 'left')
            ->join('users', 'users.id = students.user_id', 'left')
            ->where('counseling_sessions.counselor_id', $counselorId)
            ->where('counseling_sessions.status', 'Selesai')
            ->where('counseling_sessions.follow_up_plan IS NOT NULL', null, false)
            ->where('counseling_sessions.follow_up_plan !=', '')
            ->where('counseling_sessions.deleted_at', null)
            ->orderBy('counseling_sessions.session_date', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
    }

    /**
     * AJAX: Get quick stats (for auto-refresh)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getQuickStats()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }

        $counselorId = auth_id();
        $stats = $this->counselingService->getDashboardStats($counselorId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
