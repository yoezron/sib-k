<?php

/**
 * File Path: app/Controllers/HomeroomTeacher/ClassReportController.php
 * 
 * Homeroom Teacher Class Report Controller
 * Mengelola laporan kelas untuk wali kelas
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

class ClassReportController extends BaseController
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
     * Display class report index
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
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01'); // First day of current month
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t'); // Last day of current month

        // Get report data
        $reportData = $this->generateClassReport($class['id'], $startDate, $endDate);

        // Prepare data for view
        $data = [
            'title' => 'Laporan Kelas',
            'pageTitle' => 'Laporan Kelas',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('homeroom/dashboard')],
                ['title' => 'Laporan Kelas', 'url' => '#', 'active' => true],
            ],
            'class' => $class,
            'reportData' => $reportData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'currentUser' => current_user(),
        ];

        return view('homeroom_teacher/reports/index', $data);
    }

    /**
     * Get report data via AJAX
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getReportData()
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

        // Get parameters
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');

        // Generate report
        $reportData = $this->generateClassReport($class['id'], $startDate, $endDate);

        return json_success($reportData, 'Report data retrieved successfully');
    }

    /**
     * Export report to PDF
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface|void
     */
    public function exportPDF()
    {
        // Check authentication
        if (!is_logged_in() || !is_homeroom_teacher()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userId = current_user_id();
        $class = $this->getHomeroomClass($userId);

        if (!$class) {
            return redirect()->to('/homeroom/reports')
                ->with('error', 'Kelas tidak ditemukan.');
        }

        // Get parameters
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');

        // Generate report data
        $reportData = $this->generateClassReport($class['id'], $startDate, $endDate);

        // Prepare data for PDF
        $data = [
            'class' => $class,
            'reportData' => $reportData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => date('Y-m-d H:i:s'),
            'generatedBy' => current_user_name(),
        ];

        // TODO: Implement PDF generation with Dompdf (FASE 7)
        // For now, return message
        return redirect()->back()
            ->with('info', 'Fitur export PDF akan tersedia di fase berikutnya.');

        /*
        // Implementation untuk FASE 7:
        
        // Load Dompdf
        $dompdf = new \Dompdf\Dompdf();
        
        // Render view to HTML
        $html = view('homeroom_teacher/reports/pdf_template', $data);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // Set paper size
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF
        $dompdf->render();
        
        // Generate filename
        $filename = 'Laporan_Kelas_' . $class['class_name'] . '_' . date('Ymd') . '.pdf';
        
        // Stream PDF to browser
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
        */
    }

    /**
     * Export report to Excel
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface|void
     */
    public function exportExcel()
    {
        // Check authentication
        if (!is_logged_in() || !is_homeroom_teacher()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $userId = current_user_id();
        $class = $this->getHomeroomClass($userId);

        if (!$class) {
            return redirect()->to('/homeroom/reports')
                ->with('error', 'Kelas tidak ditemukan.');
        }

        // Get parameters
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');

        // Generate report data
        $reportData = $this->generateClassReport($class['id'], $startDate, $endDate);

        // TODO: Implement Excel export with PhpSpreadsheet (FASE 7)
        // For now, return message
        return redirect()->back()
            ->with('info', 'Fitur export Excel akan tersedia di fase berikutnya.');

        /*
        // Implementation untuk FASE 7:
        
        // Load PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header
        $sheet->setCellValue('A1', 'LAPORAN KELAS');
        $sheet->setCellValue('A2', 'Kelas: ' . $class['class_name']);
        $sheet->setCellValue('A3', 'Periode: ' . format_indo_date($startDate) . ' - ' . format_indo_date($endDate));
        
        // Add summary section
        $row = 5;
        $sheet->setCellValue('A' . $row, 'RINGKASAN');
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Siswa');
        $sheet->setCellValue('B' . $row, $reportData['summary']['total_students']);
        // ... add more summary data
        
        // Add student details section
        $row += 3;
        $sheet->setCellValue('A' . $row, 'DETAIL SISWA');
        $row++;
        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'NISN');
        $sheet->setCellValue('C' . $row, 'Nama');
        $sheet->setCellValue('D' . $row, 'Pelanggaran');
        $sheet->setCellValue('E' . $row, 'Poin');
        $sheet->setCellValue('F' . $row, 'Sesi Konseling');
        
        // ... add student data rows
        
        // Generate filename
        $filename = 'Laporan_Kelas_' . $class['class_name'] . '_' . date('Ymd') . '.xlsx';
        
        // Create writer
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Write to output
        $writer->save('php://output');
        exit;
        */
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
                ->select('classes.*, 
                         academic_years.year_name, 
                         academic_years.semester,
                         counselor.full_name as counselor_name')
                ->join('academic_years', 'academic_years.id = classes.academic_year_id')
                ->join('users as counselor', 'counselor.id = classes.counselor_id', 'left')
                ->where('classes.homeroom_teacher_id', $userId)
                ->where('classes.deleted_at', null)
                ->where('academic_years.is_active', 1)
                ->orderBy('classes.created_at', 'DESC')
                ->get()
                ->getRowArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM REPORT] Get class error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate comprehensive class report
     * 
     * @param int $classId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function generateClassReport($classId, $startDate, $endDate)
    {
        try {
            $report = [];

            // Get summary statistics
            $report['summary'] = $this->getReportSummary($classId, $startDate, $endDate);

            // Get student list with details
            $report['students'] = $this->getStudentDetails($classId, $startDate, $endDate);

            // Get violation summary by severity
            $report['violationBySeverity'] = $this->getViolationBySeverity($classId, $startDate, $endDate);

            // Get violation summary by category
            $report['violationByCategory'] = $this->getViolationByCategory($classId, $startDate, $endDate);

            // Get counseling session summary
            $report['sessionSummary'] = $this->getSessionSummary($classId, $startDate, $endDate);

            // Get top 5 violators
            $report['topViolators'] = $this->getTopViolators($classId, $startDate, $endDate, 5);

            // Get students needing attention (high violation points)
            $report['studentsNeedingAttention'] = $this->getStudentsNeedingAttention($classId, 50); // threshold: 50 points

            // Get monthly trends
            $report['monthlyTrends'] = $this->getMonthlyTrends($classId, 6);

            return $report;
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM REPORT] Generate report error: ' . $e->getMessage());
            return [
                'summary' => [],
                'students' => [],
                'violationBySeverity' => [],
                'violationByCategory' => [],
                'sessionSummary' => [],
                'topViolators' => [],
                'studentsNeedingAttention' => [],
                'monthlyTrends' => [],
            ];
        }
    }

    /**
     * Get report summary statistics
     * 
     * @param int $classId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function getReportSummary($classId, $startDate, $endDate)
    {
        $summary = [];

        // Total students
        $summary['total_students'] = $this->db->table('students')
            ->where('class_id', $classId)
            ->where('deleted_at', null)
            ->countAllResults();

        // Total violations in period
        $summary['total_violations'] = $this->db->table('violations')
            ->join('students', 'students.id = violations.student_id')
            ->where('students.class_id', $classId)
            ->where('violations.violation_date >=', $startDate)
            ->where('violations.violation_date <=', $endDate)
            ->where('violations.deleted_at', null)
            ->countAllResults();

        // Total violation points
        $pointsResult = $this->db->table('violations')
            ->select('SUM(violation_categories.points) as total_points')
            ->join('students', 'students.id = violations.student_id')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('students.class_id', $classId)
            ->where('violations.violation_date >=', $startDate)
            ->where('violations.violation_date <=', $endDate)
            ->where('violations.deleted_at', null)
            ->get()
            ->getRow();

        $summary['total_points'] = $pointsResult ? ($pointsResult->total_points ?? 0) : 0;

        // Students with violations
        $summary['students_with_violations'] = $this->db->table('violations')
            ->select('COUNT(DISTINCT violations.student_id) as count')
            ->join('students', 'students.id = violations.student_id')
            ->where('students.class_id', $classId)
            ->where('violations.violation_date >=', $startDate)
            ->where('violations.violation_date <=', $endDate)
            ->where('violations.deleted_at', null)
            ->get()
            ->getRow()
            ->count ?? 0;

        // Total counseling sessions
        $summary['total_sessions'] = $this->db->table('counseling_sessions')
            ->join('students', 'students.id = counseling_sessions.student_id')
            ->where('students.class_id', $classId)
            ->where('counseling_sessions.session_date >=', $startDate)
            ->where('counseling_sessions.session_date <=', $endDate)
            ->where('counseling_sessions.deleted_at', null)
            ->countAllResults();

        // Students in counseling
        $summary['students_in_counseling'] = $this->db->table('counseling_sessions')
            ->select('COUNT(DISTINCT counseling_sessions.student_id) as count')
            ->join('students', 'students.id = counseling_sessions.student_id')
            ->where('students.class_id', $classId)
            ->where('counseling_sessions.session_date >=', $startDate)
            ->where('counseling_sessions.session_date <=', $endDate)
            ->where('counseling_sessions.deleted_at', null)
            ->get()
            ->getRow()
            ->count ?? 0;

        // Average violations per student
        $summary['avg_violations_per_student'] = $summary['total_students'] > 0
            ? round($summary['total_violations'] / $summary['total_students'], 2)
            : 0;

        // Gender distribution
        $genderDist = $this->db->table('students')
            ->select('gender, COUNT(*) as count')
            ->where('class_id', $classId)
            ->where('deleted_at', null)
            ->groupBy('gender')
            ->get()
            ->getResultArray();

        $summary['gender_distribution'] = [
            'Laki-laki' => 0,
            'Perempuan' => 0,
        ];

        foreach ($genderDist as $gender) {
            $summary['gender_distribution'][$gender['gender']] = $gender['count'];
        }

        return $summary;
    }

    /**
     * Get student details with violation and counseling data
     * 
     * @param int $classId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function getStudentDetails($classId, $startDate, $endDate)
    {
        try {
            $students = $this->db->table('students')
                ->select('students.id, students.nisn, students.full_name, students.gender')
                ->where('students.class_id', $classId)
                ->where('students.deleted_at', null)
                ->orderBy('students.full_name', 'ASC')
                ->get()
                ->getResultArray();

            // Enrich each student with violation and counseling data
            foreach ($students as &$student) {
                // Count violations
                $violationCount = $this->db->table('violations')
                    ->where('student_id', $student['id'])
                    ->where('violation_date >=', $startDate)
                    ->where('violation_date <=', $endDate)
                    ->where('deleted_at', null)
                    ->countAllResults();

                $student['violation_count'] = $violationCount;

                // Sum violation points
                $pointsResult = $this->db->table('violations')
                    ->select('SUM(violation_categories.points) as total_points')
                    ->join('violation_categories', 'violation_categories.id = violations.category_id')
                    ->where('violations.student_id', $student['id'])
                    ->where('violations.violation_date >=', $startDate)
                    ->where('violations.violation_date <=', $endDate)
                    ->where('violations.deleted_at', null)
                    ->get()
                    ->getRow();

                $student['total_points'] = $pointsResult ? ($pointsResult->total_points ?? 0) : 0;

                // Count counseling sessions
                $sessionCount = $this->db->table('counseling_sessions')
                    ->where('student_id', $student['id'])
                    ->where('session_date >=', $startDate)
                    ->where('session_date <=', $endDate)
                    ->where('deleted_at', null)
                    ->countAllResults();

                $student['session_count'] = $sessionCount;

                // Determine status based on points
                if ($student['total_points'] >= 100) {
                    $student['status'] = 'Kritis';
                    $student['status_class'] = 'danger';
                } elseif ($student['total_points'] >= 50) {
                    $student['status'] = 'Perlu Perhatian';
                    $student['status_class'] = 'warning';
                } elseif ($student['total_points'] > 0) {
                    $student['status'] = 'Waspada';
                    $student['status_class'] = 'info';
                } else {
                    $student['status'] = 'Baik';
                    $student['status_class'] = 'success';
                }
            }

            return $students;
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM REPORT] Get student details error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get violations grouped by severity
     * 
     * @param int $classId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function getViolationBySeverity($classId, $startDate, $endDate)
    {
        try {
            return $this->db->table('violation_categories')
                ->select('violation_categories.severity, COUNT(violations.id) as count')
                ->join('violations', 'violations.category_id = violation_categories.id AND violations.deleted_at IS NULL', 'left')
                ->join('students', 'students.id = violations.student_id', 'left')
                ->where('students.class_id', $classId)
                ->where('violations.violation_date >=', $startDate)
                ->where('violations.violation_date <=', $endDate)
                ->groupBy('violation_categories.severity')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM REPORT] Get violation by severity error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get violations grouped by category
     * 
     * @param int $classId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function getViolationByCategory($classId, $startDate, $endDate)
    {
        try {
            return $this->db->table('violation_categories')
                ->select('violation_categories.category_name, 
                         violation_categories.severity,
                         COUNT(violations.id) as count')
                ->join('violations', 'violations.category_id = violation_categories.id AND violations.deleted_at IS NULL', 'left')
                ->join('students', 'students.id = violations.student_id', 'left')
                ->where('students.class_id', $classId)
                ->where('violations.violation_date >=', $startDate)
                ->where('violations.violation_date <=', $endDate)
                ->groupBy('violation_categories.id')
                ->orderBy('count', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM REPORT] Get violation by category error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get counseling session summary
     * 
     * @param int $classId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function getSessionSummary($classId, $startDate, $endDate)
    {
        try {
            $summary = [];

            // Group by session type
            $byType = $this->db->table('counseling_sessions')
                ->select('counseling_sessions.session_type, COUNT(*) as count')
                ->join('students', 'students.id = counseling_sessions.student_id')
                ->where('students.class_id', $classId)
                ->where('counseling_sessions.session_date >=', $startDate)
                ->where('counseling_sessions.session_date <=', $endDate)
                ->where('counseling_sessions.deleted_at', null)
                ->groupBy('counseling_sessions.session_type')
                ->get()
                ->getResultArray();

            $summary['by_type'] = $byType;

            // Group by status
            $byStatus = $this->db->table('counseling_sessions')
                ->select('counseling_sessions.status, COUNT(*) as count')
                ->join('students', 'students.id = counseling_sessions.student_id')
                ->where('students.class_id', $classId)
                ->where('counseling_sessions.session_date >=', $startDate)
                ->where('counseling_sessions.session_date <=', $endDate)
                ->where('counseling_sessions.deleted_at', null)
                ->groupBy('counseling_sessions.status')
                ->get()
                ->getResultArray();

            $summary['by_status'] = $byStatus;

            return $summary;
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM REPORT] Get session summary error: ' . $e->getMessage());
            return ['by_type' => [], 'by_status' => []];
        }
    }

    /**
     * Get top violators in period
     * 
     * @param int $classId
     * @param string $startDate
     * @param string $endDate
     * @param int $limit
     * @return array
     */
    private function getTopViolators($classId, $startDate, $endDate, $limit = 5)
    {
        try {
            return $this->db->table('students')
                ->select('students.id, students.nisn, students.full_name,
                         COUNT(violations.id) as violation_count,
                         SUM(violation_categories.points) as total_points')
                ->join('violations', 'violations.student_id = students.id AND violations.deleted_at IS NULL', 'left')
                ->join('violation_categories', 'violation_categories.id = violations.category_id', 'left')
                ->where('students.class_id', $classId)
                ->where('violations.violation_date >=', $startDate)
                ->where('violations.violation_date <=', $endDate)
                ->groupBy('students.id')
                ->having('violation_count >', 0)
                ->orderBy('total_points', 'DESC')
                ->orderBy('violation_count', 'DESC')
                ->limit($limit)
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM REPORT] Get top violators error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get students needing attention (high violation points)
     * 
     * @param int $classId
     * @param int $threshold
     * @return array
     */
    private function getStudentsNeedingAttention($classId, $threshold = 50)
    {
        try {
            return $this->db->table('students')
                ->select('students.id, students.nisn, students.full_name,
                         SUM(violation_categories.points) as total_points,
                         COUNT(violations.id) as violation_count')
                ->join('violations', 'violations.student_id = students.id AND violations.deleted_at IS NULL', 'left')
                ->join('violation_categories', 'violation_categories.id = violations.category_id', 'left')
                ->where('students.class_id', $classId)
                ->where('students.deleted_at', null)
                ->groupBy('students.id')
                ->having('total_points >=', $threshold)
                ->orderBy('total_points', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM REPORT] Get students needing attention error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get monthly violation trends
     * 
     * @param int $classId
     * @param int $months
     * @return array
     */
    private function getMonthlyTrends($classId, $months = 6)
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
                    'month_key' => $month,
                    'count' => $count,
                ];
            }

            return $trends;
        } catch (\Exception $e) {
            log_message('error', '[HOMEROOM REPORT] Get monthly trends error: ' . $e->getMessage());
            return [];
        }
    }
}
