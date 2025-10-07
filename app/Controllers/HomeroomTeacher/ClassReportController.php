<?php

/**
 * File Path: app/Controllers/HomeroomTeacher/ClassReportController.php
 * 
 * Class Report Controller
 * Mengelola laporan kelas untuk wali kelas
 * 
 * @package    SIB-K
 * @subpackage Controllers\HomeroomTeacher
 * @category   Controller
 * @author     Development Team
 * @created    2025-01-07
 * @version    1.0.0
 */

namespace App\Controllers\HomeroomTeacher;

use App\Controllers\BaseController;
use App\Services\ClassService;
use App\Services\ViolationService;
use App\Models\ClassModel;
use App\Models\StudentModel;
use App\Models\ViolationModel;
use App\Models\CounselingSessionModel;
use App\Models\AcademicYearModel;

class ClassReportController extends BaseController
{
    protected $classService;
    protected $violationService;
    protected $classModel;
    protected $studentModel;
    protected $violationModel;
    protected $sessionModel;
    protected $academicYearModel;
    protected $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->classService = new ClassService();
        $this->violationService = new ViolationService();
        $this->classModel = new ClassModel();
        $this->studentModel = new StudentModel();
        $this->violationModel = new ViolationModel();
        $this->sessionModel = new CounselingSessionModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Display class report summary
     * 
     * @return string|RedirectResponse
     */
    public function index()
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_wali_kelas()) {
            return redirect()->to('/')->with('error', 'Akses ditolak. Halaman ini hanya untuk Wali Kelas.');
        }

        // Get homeroom class for this teacher
        $homeroomClass = $this->getHomeroomClass();

        if (!$homeroomClass) {
            return redirect()->to('homeroom/dashboard')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Get report data
        $reportData = $this->getClassReport($homeroomClass['id']);

        // Page metadata
        $data = [
            'title' => 'Laporan Kelas - ' . $homeroomClass['class_name'],
            'pageTitle' => 'Laporan Kelas ' . $homeroomClass['class_name'],
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('homeroom/dashboard')],
                ['title' => 'Laporan Kelas', 'url' => '#', 'active' => true],
            ],
            'class' => $homeroomClass,
            'report' => $reportData,
        ];

        return view('homeroom_teacher/reports/class_summary', $data);
    }

    /**
     * Get homeroom class for logged in teacher
     * 
     * @return array|null
     */
    private function getHomeroomClass()
    {
        return $this->classModel
            ->select('classes.*, 
                      academic_years.year_name, 
                      academic_years.semester,
                      academic_years.is_active as year_active')
            ->join('academic_years', 'academic_years.id = classes.academic_year_id')
            ->where('classes.homeroom_teacher_id', auth_id())
            ->where('classes.is_active', 1)
            ->where('academic_years.is_active', 1)
            ->first();
    }

    /**
     * Get comprehensive class report data
     * 
     * @param int $classId
     * @return array
     */
    private function getClassReport($classId)
    {
        $report = [];

        // 1. Student Statistics
        $report['student_stats'] = $this->getStudentStatistics($classId);

        // 2. Gender Distribution
        $report['gender_distribution'] = $this->getGenderDistribution($classId);

        // 3. Violation Statistics
        $report['violation_stats'] = $this->getViolationStatistics($classId);

        // 4. Counseling Session Statistics
        $report['session_stats'] = $this->getSessionStatistics($classId);

        // 5. Top Violations
        $report['top_violations'] = $this->getTopViolations($classId);

        // 6. Students at Risk (high violation points)
        $report['students_at_risk'] = $this->getStudentsAtRisk($classId);

        // 7. Recent Violations (last 30 days)
        $report['recent_violations'] = $this->getRecentViolations($classId);

        // 8. All Students with Details
        $report['students'] = $this->getStudentsWithDetails($classId);

        // 9. Monthly Violation Trend (last 6 months)
        $report['violation_trend'] = $this->getViolationTrend($classId);

        // 10. Session Attendance Rate
        $report['session_attendance'] = $this->getSessionAttendance($classId);

        return $report;
    }

    /**
     * Get student statistics
     * 
     * @param int $classId
     * @return array
     */
    private function getStudentStatistics($classId)
    {
        $total = $this->studentModel
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->countAllResults();

        $active = $this->studentModel
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->countAllResults();

        $inactive = $this->studentModel
            ->where('class_id', $classId)
            ->whereIn('status', ['Tidak Aktif', 'Pindah', 'Lulus', 'Drop Out'])
            ->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
        ];
    }

    /**
     * Get gender distribution
     * 
     * @param int $classId
     * @return array
     */
    private function getGenderDistribution($classId)
    {
        $genderStats = $this->db->table('students')
            ->select('gender, COUNT(*) as count')
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->where('deleted_at', null)
            ->groupBy('gender')
            ->get()
            ->getResultArray();

        $distribution = [
            'L' => 0,
            'P' => 0,
        ];

        foreach ($genderStats as $stat) {
            $distribution[$stat['gender']] = (int)$stat['count'];
        }

        return $distribution;
    }

    /**
     * Get violation statistics
     * 
     * @param int $classId
     * @return array
     */
    private function getViolationStatistics($classId)
    {
        // Get student IDs in this class
        $studentIds = $this->studentModel
            ->select('id')
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->findAll();

        if (empty($studentIds)) {
            return [
                'total' => 0,
                'ringan' => 0,
                'sedang' => 0,
                'berat' => 0,
                'resolved' => 0,
                'pending' => 0,
            ];
        }

        $studentIdList = array_column($studentIds, 'id');

        // Total violations
        $total = $this->violationModel
            ->whereIn('student_id', $studentIdList)
            ->countAllResults();

        // By severity
        $ringan = $this->violationModel
            ->whereIn('student_id', $studentIdList)
            ->where('severity_level', 'Ringan')
            ->countAllResults();

        $sedang = $this->violationModel
            ->whereIn('student_id', $studentIdList)
            ->where('severity_level', 'Sedang')
            ->countAllResults();

        $berat = $this->violationModel
            ->whereIn('student_id', $studentIdList)
            ->where('severity_level', 'Berat')
            ->countAllResults();

        // By status
        $resolved = $this->violationModel
            ->whereIn('student_id', $studentIdList)
            ->where('status', 'Selesai')
            ->countAllResults();

        $pending = $this->violationModel
            ->whereIn('student_id', $studentIdList)
            ->whereIn('status', ['Dilaporkan', 'Dalam Proses'])
            ->countAllResults();

        return [
            'total' => $total,
            'ringan' => $ringan,
            'sedang' => $sedang,
            'berat' => $berat,
            'resolved' => $resolved,
            'pending' => $pending,
        ];
    }

    /**
     * Get counseling session statistics
     * 
     * @param int $classId
     * @return array
     */
    private function getSessionStatistics($classId)
    {
        // Get student IDs in this class
        $studentIds = $this->studentModel
            ->select('id')
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->findAll();

        if (empty($studentIds)) {
            return [
                'total' => 0,
                'completed' => 0,
                'scheduled' => 0,
                'cancelled' => 0,
            ];
        }

        $studentIdList = array_column($studentIds, 'id');

        // Total sessions
        $total = $this->sessionModel
            ->whereIn('student_id', $studentIdList)
            ->countAllResults();

        // By status
        $completed = $this->sessionModel
            ->whereIn('student_id', $studentIdList)
            ->where('status', 'Selesai')
            ->countAllResults();

        $scheduled = $this->sessionModel
            ->whereIn('student_id', $studentIdList)
            ->where('status', 'Terjadwal')
            ->countAllResults();

        $cancelled = $this->sessionModel
            ->whereIn('student_id', $studentIdList)
            ->where('status', 'Dibatalkan')
            ->countAllResults();

        return [
            'total' => $total,
            'completed' => $completed,
            'scheduled' => $scheduled,
            'cancelled' => $cancelled,
        ];
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
        // Get student IDs in this class
        $studentIds = $this->studentModel
            ->select('id')
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->findAll();

        if (empty($studentIds)) {
            return [];
        }

        $studentIdList = array_column($studentIds, 'id');

        return $this->db->table('violations')
            ->select('violation_categories.category_name, 
                      violation_categories.severity_level,
                      COUNT(*) as total_count')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->whereIn('violations.student_id', $studentIdList)
            ->where('violations.deleted_at', null)
            ->groupBy('violations.category_id')
            ->orderBy('total_count', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get students at risk (high violation points)
     * 
     * @param int $classId
     * @param int $threshold
     * @return array
     */
    private function getStudentsAtRisk($classId, $threshold = 50)
    {
        return $this->db->table('students')
            ->select('students.*, 
                      users.full_name, 
                      users.email,
                      students.violation_points,
                      COUNT(violations.id) as violation_count')
            ->join('users', 'users.id = students.user_id')
            ->join('violations', 'violations.student_id = students.id', 'left')
            ->where('students.class_id', $classId)
            ->where('students.status', 'Aktif')
            ->where('students.violation_points >=', $threshold)
            ->where('students.deleted_at', null)
            ->groupBy('students.id')
            ->orderBy('students.violation_points', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get recent violations (last 30 days)
     * 
     * @param int $classId
     * @param int $days
     * @return array
     */
    private function getRecentViolations($classId, $days = 30)
    {
        // Get student IDs in this class
        $studentIds = $this->studentModel
            ->select('id')
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->findAll();

        if (empty($studentIds)) {
            return [];
        }

        $studentIdList = array_column($studentIds, 'id');
        $dateFrom = date('Y-m-d', strtotime("-{$days} days"));

        return $this->db->table('violations')
            ->select('violations.*, 
                      users.full_name as student_name,
                      violation_categories.category_name,
                      violation_categories.severity_level,
                      reporter.full_name as reporter_name')
            ->join('students', 'students.id = violations.student_id')
            ->join('users', 'users.id = students.user_id')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->join('users as reporter', 'reporter.id = violations.reported_by', 'left')
            ->whereIn('violations.student_id', $studentIdList)
            ->where('violations.violation_date >=', $dateFrom)
            ->where('violations.deleted_at', null)
            ->orderBy('violations.violation_date', 'DESC')
            ->orderBy('violations.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
    }

    /**
     * Get all students with details
     * 
     * @param int $classId
     * @return array
     */
    private function getStudentsWithDetails($classId)
    {
        return $this->db->table('students')
            ->select('students.*, 
                      users.full_name, 
                      users.email,
                      users.phone,
                      students.violation_points,
                      COUNT(DISTINCT violations.id) as violation_count,
                      COUNT(DISTINCT counseling_sessions.id) as session_count')
            ->join('users', 'users.id = students.user_id')
            ->join('violations', 'violations.student_id = students.id AND violations.deleted_at IS NULL', 'left')
            ->join('counseling_sessions', 'counseling_sessions.student_id = students.id AND counseling_sessions.deleted_at IS NULL', 'left')
            ->where('students.class_id', $classId)
            ->where('students.status', 'Aktif')
            ->where('students.deleted_at', null)
            ->groupBy('students.id')
            ->orderBy('users.full_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get violation trend (monthly data for last 6 months)
     * 
     * @param int $classId
     * @param int $months
     * @return array
     */
    private function getViolationTrend($classId, $months = 6)
    {
        // Get student IDs in this class
        $studentIds = $this->studentModel
            ->select('id')
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->findAll();

        if (empty($studentIds)) {
            return [];
        }

        $studentIdList = array_column($studentIds, 'id');
        $dateFrom = date('Y-m-01', strtotime("-{$months} months"));

        return $this->db->table('violations')
            ->select("DATE_FORMAT(violation_date, '%Y-%m') as month, 
                      COUNT(*) as total_count")
            ->whereIn('student_id', $studentIdList)
            ->where('violation_date >=', $dateFrom)
            ->where('deleted_at', null)
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get session attendance rate
     * 
     * @param int $classId
     * @return array
     */
    private function getSessionAttendance($classId)
    {
        // Get student IDs in this class
        $studentIds = $this->studentModel
            ->select('id')
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->findAll();

        if (empty($studentIds)) {
            return [
                'total_sessions' => 0,
                'attended' => 0,
                'not_attended' => 0,
                'attendance_rate' => 0,
            ];
        }

        $studentIdList = array_column($studentIds, 'id');

        $totalSessions = $this->sessionModel
            ->whereIn('student_id', $studentIdList)
            ->whereIn('status', ['Selesai', 'Tidak Hadir'])
            ->countAllResults();

        $attended = $this->sessionModel
            ->whereIn('student_id', $studentIdList)
            ->where('status', 'Selesai')
            ->countAllResults();

        $notAttended = $this->sessionModel
            ->whereIn('student_id', $studentIdList)
            ->where('status', 'Tidak Hadir')
            ->countAllResults();

        $attendanceRate = $totalSessions > 0 ? round(($attended / $totalSessions) * 100, 2) : 0;

        return [
            'total_sessions' => $totalSessions,
            'attended' => $attended,
            'not_attended' => $notAttended,
            'attendance_rate' => $attendanceRate,
        ];
    }

    /**
     * Export class report to PDF
     * 
     * @return ResponseInterface
     */
    public function exportPDF()
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_wali_kelas()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Get homeroom class
        $homeroomClass = $this->getHomeroomClass();

        if (!$homeroomClass) {
            return redirect()->to('homeroom/dashboard')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Get report data
        $reportData = $this->getClassReport($homeroomClass['id']);

        // Prepare data for PDF
        $data = [
            'class' => $homeroomClass,
            'report' => $reportData,
            'generated_at' => date('d/m/Y H:i:s'),
            'generated_by' => auth_user()['full_name'],
        ];

        // Load PDF library (using DOMPDF or similar)
        // Note: You need to install dompdf via composer first
        // composer require dompdf/dompdf

        $dompdf = new \Dompdf\Dompdf();
        $html = view('homeroom_teacher/reports/pdf_template', $data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Laporan_Kelas_' . $homeroomClass['class_name'] . '_' . date('Y-m-d') . '.pdf';

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    /**
     * Export class report to Excel
     * 
     * @return ResponseInterface
     */
    public function exportExcel()
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_wali_kelas()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Get homeroom class
        $homeroomClass = $this->getHomeroomClass();

        if (!$homeroomClass) {
            return redirect()->to('homeroom/dashboard')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Get report data
        $reportData = $this->getClassReport($homeroomClass['id']);

        // Load PhpSpreadsheet library
        // Note: You need to install phpspreadsheet via composer first
        // composer require phpoffice/phpspreadsheet

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'LAPORAN KELAS');
        $sheet->setCellValue('A2', 'Kelas: ' . $homeroomClass['class_name']);
        $sheet->setCellValue('A3', 'Tahun Ajaran: ' . $homeroomClass['year_name']);
        $sheet->setCellValue('A4', 'Wali Kelas: ' . auth_user()['full_name']);
        $sheet->setCellValue('A5', 'Tanggal: ' . date('d/m/Y'));

        // Student data headers
        $row = 7;
        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'NISN');
        $sheet->setCellValue('C' . $row, 'Nama Lengkap');
        $sheet->setCellValue('D' . $row, 'L/P');
        $sheet->setCellValue('E' . $row, 'Poin Pelanggaran');
        $sheet->setCellValue('F' . $row, 'Jumlah Pelanggaran');
        $sheet->setCellValue('G' . $row, 'Jumlah Konseling');
        $sheet->setCellValue('H' . $row, 'Status');

        // Student data
        $row++;
        $no = 1;
        foreach ($reportData['students'] as $student) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $student['nisn']);
            $sheet->setCellValue('C' . $row, $student['full_name']);
            $sheet->setCellValue('D' . $row, $student['gender']);
            $sheet->setCellValue('E' . $row, $student['violation_points']);
            $sheet->setCellValue('F' . $row, $student['violation_count']);
            $sheet->setCellValue('G' . $row, $student['session_count']);
            $sheet->setCellValue('H' . $row, $student['status']);
            $row++;
        }

        // Statistics section
        $row += 2;
        $sheet->setCellValue('A' . $row, 'STATISTIK KELAS');
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Siswa:');
        $sheet->setCellValue('B' . $row, $reportData['student_stats']['total']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Laki-laki:');
        $sheet->setCellValue('B' . $row, $reportData['gender_distribution']['L']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Perempuan:');
        $sheet->setCellValue('B' . $row, $reportData['gender_distribution']['P']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Pelanggaran:');
        $sheet->setCellValue('B' . $row, $reportData['violation_stats']['total']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Sesi Konseling:');
        $sheet->setCellValue('B' . $row, $reportData['session_stats']['total']);

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'Laporan_Kelas_' . $homeroomClass['class_name'] . '_' . date('Y-m-d') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Get class report data via AJAX
     * For dynamic updates
     * 
     * @return ResponseInterface
     */
    public function getReportData()
    {
        // Check authentication
        if (!is_logged_in() || !is_wali_kelas()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
        }

        // Get homeroom class
        $homeroomClass = $this->getHomeroomClass();

        if (!$homeroomClass) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kelas tidak ditemukan'
            ]);
        }

        // Get report data
        $reportData = $this->getClassReport($homeroomClass['id']);

        return $this->response->setJSON([
            'success' => true,
            'data' => $reportData
        ]);
    }
}
