<?php

/**
 * File Path: app/Controllers/HomeroomTeacher/ClassReportController.php
 * 
 * Class Report Controller - OPTIMIZED VERSION
 * 
 * CHANGES:
 * ✅ OPTIMIZED: Simplified queries (students.full_name sudah ada)
 * ✅ FIXED: Consistent use of total_violation_points
 * ✅ IMPROVED: Better performance with reduced joins
 * ✅ VERIFIED: All columns match database structure
 * 
 * @package    SIB-K
 * @subpackage Controllers\HomeroomTeacher
 * @version    2.0.0 - OPTIMIZED
 * @updated    2025-01-07
 */

namespace App\Controllers\HomeroomTeacher;

use App\Controllers\BaseController;
use App\Models\ClassModel;
use App\Models\StudentModel;
use App\Models\ViolationModel;
use App\Models\CounselingSessionModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class ClassReportController extends BaseController
{
    protected $classModel;
    protected $studentModel;
    protected $violationModel;
    protected $sessionModel;
    protected $db;

    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->studentModel = new StudentModel();
        $this->violationModel = new ViolationModel();
        $this->sessionModel = new CounselingSessionModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Display class report summary
     */
    public function index()
    {
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_wali_kelas()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $userId = auth_id();
        $homeroomClass = $this->getHomeroomClass($userId);

        if (!$homeroomClass) {
            return view('homeroom_teacher/no_class', [
                'title' => 'Laporan Kelas',
                'pageTitle' => 'Tidak Ada Kelas',
                'message' => 'Anda belum ditugaskan sebagai wali kelas untuk tahun ajaran aktif.',
            ]);
        }

        // Get comprehensive class report
        $reportData = $this->getClassReport($homeroomClass['id']);

        $data = [
            'title' => 'Laporan Kelas',
            'pageTitle' => 'Laporan Kelas - ' . $homeroomClass['class_name'],
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route_to('homeroom.dashboard')],
                ['title' => 'Laporan', 'url' => '#', 'active' => true],
            ],
            'homeroom_class' => $homeroomClass,
            'report' => $reportData,
        ];

        return view('homeroom_teacher/reports/class_summary', $data);
    }

    /**
     * Get report data (AJAX)
     */
    public function getReportData()
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

        $reportData = $this->getClassReport($homeroomClass['id']);

        return $this->response->setJSON([
            'success' => true,
            'data' => $reportData
        ]);
    }

    /**
     * Export report to PDF
     */
    public function exportPDF()
    {
        if (!is_logged_in() || !is_wali_kelas()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        $userId = auth_id();
        $homeroomClass = $this->getHomeroomClass($userId);

        if (!$homeroomClass) {
            return redirect()->to(route_to('homeroom.dashboard'))
                ->with('error', 'Kelas tidak ditemukan');
        }

        $reportData = $this->getClassReport($homeroomClass['id']);

        // Prepare data for PDF view
        $data = [
            'homeroom_class' => $homeroomClass,
            'report' => $reportData,
            'generated_date' => date('d F Y'),
            'generated_by' => auth_name(),
        ];

        // Generate PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $html = view('homeroom_teacher/reports/pdf_template', $data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Laporan_Kelas_' . $homeroomClass['class_name'] . '_' . date('Y-m-d') . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    /**
     * Export report to Excel
     */
    public function exportExcel()
    {
        if (!is_logged_in() || !is_wali_kelas()) {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        $userId = auth_id();
        $homeroomClass = $this->getHomeroomClass($userId);

        if (!$homeroomClass) {
            return redirect()->to(route_to('homeroom.dashboard'))
                ->with('error', 'Kelas tidak ditemukan');
        }

        $reportData = $this->getClassReport($homeroomClass['id']);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'LAPORAN KELAS');
        $sheet->setCellValue('A2', 'Kelas: ' . $homeroomClass['class_name']);
        $sheet->setCellValue('A3', 'Tahun Ajaran: ' . $homeroomClass['year_name']);
        $sheet->setCellValue('A4', 'Wali Kelas: ' . auth_name());
        $sheet->setCellValue('A5', 'Tanggal: ' . date('d/m/Y'));

        // Student data table header
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

        // Statistics
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

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'Laporan_Kelas_' . $homeroomClass['class_name'] . '_' . date('Y-m-d') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Get homeroom class
     */
    private function getHomeroomClass($userId)
    {
        return $this->classModel
            ->select('classes.*, academic_years.year_name, academic_years.semester')
            ->join('academic_years', 'academic_years.id = classes.academic_year_id', 'left')
            ->where('classes.homeroom_teacher_id', $userId)
            ->where('classes.is_active', 1)
            ->where('academic_years.is_active', 1)
            ->first();
    }

    /**
     * Get comprehensive class report
     * ✅ OPTIMIZED: Simplified queries
     */
    private function getClassReport($classId)
    {
        return [
            'students' => $this->getStudentsWithDetails($classId),
            'student_stats' => $this->getStudentStatistics($classId),
            'gender_distribution' => $this->getGenderDistribution($classId),
            'violation_stats' => $this->getViolationStatistics($classId),
            'session_stats' => $this->getSessionStatistics($classId),
            'top_violations' => $this->getTopViolations($classId, 5),
            'students_at_risk' => $this->getStudentsAtRisk($classId, 50),
            'recent_violations' => $this->getRecentViolations($classId, 30),
            'violation_trend' => $this->getViolationTrend($classId, 6),
            'session_attendance' => $this->getSessionAttendance($classId),
        ];
    }

    /**
     * ✅ OPTIMIZED: Direct use of students.full_name
     */
    private function getStudentsWithDetails($classId)
    {
        $query = "
            SELECT 
                s.id,
                s.nisn,
                s.full_name,
                s.gender,
                s.status,
                s.total_violation_points as violation_points,
                u.email,
                u.phone,
                COUNT(DISTINCT v.id) as violation_count,
                COUNT(DISTINCT cs.id) as session_count
            FROM students s
            INNER JOIN users u ON u.id = s.user_id
            LEFT JOIN violations v ON v.student_id = s.id AND v.deleted_at IS NULL
            LEFT JOIN counseling_sessions cs ON cs.student_id = s.id AND cs.deleted_at IS NULL
            WHERE s.class_id = ?
              AND s.status = 'Aktif'
              AND s.deleted_at IS NULL
            GROUP BY s.id
            ORDER BY s.full_name ASC
        ";

        return $this->db->query($query, [$classId])->getResultArray();
    }

    private function getStudentStatistics($classId)
    {
        $query = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN gender = 'L' THEN 1 ELSE 0 END) as male,
                SUM(CASE WHEN gender = 'P' THEN 1 ELSE 0 END) as female,
                SUM(CASE WHEN status = 'Aktif' THEN 1 ELSE 0 END) as active
            FROM students
            WHERE class_id = ?
              AND deleted_at IS NULL
        ";

        $result = $this->db->query($query, [$classId])->getRowArray();

        return [
            'total' => (int)($result['total'] ?? 0),
            'male' => (int)($result['male'] ?? 0),
            'female' => (int)($result['female'] ?? 0),
            'active' => (int)($result['active'] ?? 0),
        ];
    }

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

        $distribution = ['L' => 0, 'P' => 0];

        foreach ($genderStats as $stat) {
            $distribution[$stat['gender']] = (int)$stat['count'];
        }

        return $distribution;
    }

    private function getViolationStatistics($classId)
    {
        $query = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN vc.severity_level = 'Ringan' THEN 1 ELSE 0 END) as ringan,
                SUM(CASE WHEN vc.severity_level = 'Sedang' THEN 1 ELSE 0 END) as sedang,
                SUM(CASE WHEN vc.severity_level = 'Berat' THEN 1 ELSE 0 END) as berat,
                SUM(CASE WHEN v.status = 'Selesai' THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN v.status IN ('Dilaporkan', 'Dalam Proses') THEN 1 ELSE 0 END) as pending
            FROM violations v
            INNER JOIN students s ON s.id = v.student_id
            INNER JOIN violation_categories vc ON vc.id = v.category_id
            WHERE s.class_id = ?
              AND s.status = 'Aktif'
              AND v.deleted_at IS NULL
        ";

        $result = $this->db->query($query, [$classId])->getRowArray();

        return [
            'total' => (int)($result['total'] ?? 0),
            'ringan' => (int)($result['ringan'] ?? 0),
            'sedang' => (int)($result['sedang'] ?? 0),
            'berat' => (int)($result['berat'] ?? 0),
            'resolved' => (int)($result['resolved'] ?? 0),
            'pending' => (int)($result['pending'] ?? 0),
        ];
    }

    private function getSessionStatistics($classId)
    {
        $query = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN cs.status = 'Selesai' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN cs.status = 'Terjadwal' THEN 1 ELSE 0 END) as scheduled,
                SUM(CASE WHEN cs.status = 'Dibatalkan' THEN 1 ELSE 0 END) as cancelled
            FROM counseling_sessions cs
            INNER JOIN students s ON s.id = cs.student_id
            WHERE s.class_id = ?
              AND s.status = 'Aktif'
              AND cs.deleted_at IS NULL
        ";

        $result = $this->db->query($query, [$classId])->getRowArray();

        return [
            'total' => (int)($result['total'] ?? 0),
            'completed' => (int)($result['completed'] ?? 0),
            'scheduled' => (int)($result['scheduled'] ?? 0),
            'cancelled' => (int)($result['cancelled'] ?? 0),
        ];
    }

    private function getTopViolations($classId, $limit = 5)
    {
        $query = "
            SELECT 
                vc.category_name,
                vc.severity_level,
                COUNT(*) as total_count
            FROM violations v
            INNER JOIN students s ON s.id = v.student_id
            INNER JOIN violation_categories vc ON vc.id = v.category_id
            WHERE s.class_id = ?
              AND s.status = 'Aktif'
              AND v.deleted_at IS NULL
            GROUP BY v.category_id
            ORDER BY total_count DESC
            LIMIT ?
        ";

        return $this->db->query($query, [$classId, $limit])->getResultArray();
    }

    /**
     * ✅ OPTIMIZED: Using students.total_violation_points directly
     */
    private function getStudentsAtRisk($classId, $threshold = 50)
    {
        $query = "
            SELECT 
                s.id,
                s.nisn,
                s.full_name,
                s.gender,
                s.status,
                s.total_violation_points as violation_points,
                u.email,
                COUNT(v.id) as violation_count
            FROM students s
            INNER JOIN users u ON u.id = s.user_id
            LEFT JOIN violations v ON v.student_id = s.id AND v.deleted_at IS NULL
            WHERE s.class_id = ?
              AND s.status = 'Aktif'
              AND s.deleted_at IS NULL
              AND s.total_violation_points >= ?
            GROUP BY s.id
            ORDER BY s.total_violation_points DESC
        ";

        return $this->db->query($query, [$classId, $threshold])->getResultArray();
    }

    /**
     * ✅ OPTIMIZED: Direct use of students.full_name
     */
    private function getRecentViolations($classId, $days = 30)
    {
        $dateFrom = date('Y-m-d', strtotime("-{$days} days"));

        $query = "
            SELECT 
                v.*,
                s.full_name as student_name,
                vc.category_name,
                vc.severity_level,
                u.full_name as reporter_name
            FROM violations v
            INNER JOIN students s ON s.id = v.student_id
            INNER JOIN violation_categories vc ON vc.id = v.category_id
            LEFT JOIN users u ON u.id = v.reported_by
            WHERE s.class_id = ?
              AND s.status = 'Aktif'
              AND v.violation_date >= ?
              AND v.deleted_at IS NULL
            ORDER BY v.violation_date DESC, v.created_at DESC
            LIMIT 10
        ";

        return $this->db->query($query, [$classId, $dateFrom])->getResultArray();
    }

    private function getViolationTrend($classId, $months = 6)
    {
        $dateFrom = date('Y-m-01', strtotime("-{$months} months"));

        $query = "
            SELECT 
                DATE_FORMAT(v.violation_date, '%Y-%m') as month,
                COUNT(*) as total_count
            FROM violations v
            INNER JOIN students s ON s.id = v.student_id
            WHERE s.class_id = ?
              AND s.status = 'Aktif'
              AND v.violation_date >= ?
              AND v.deleted_at IS NULL
            GROUP BY month
            ORDER BY month ASC
        ";

        return $this->db->query($query, [$classId, $dateFrom])->getResultArray();
    }

    private function getSessionAttendance($classId)
    {
        $query = "
            SELECT 
                COUNT(*) as total_sessions,
                SUM(CASE WHEN cs.status = 'Selesai' THEN 1 ELSE 0 END) as attended,
                SUM(CASE WHEN cs.status = 'Tidak Hadir' THEN 1 ELSE 0 END) as not_attended
            FROM counseling_sessions cs
            INNER JOIN students s ON s.id = cs.student_id
            WHERE s.class_id = ?
              AND s.status = 'Aktif'
              AND cs.status IN ('Selesai', 'Tidak Hadir')
              AND cs.deleted_at IS NULL
        ";

        $result = $this->db->query($query, [$classId])->getRowArray();

        $totalSessions = (int)($result['total_sessions'] ?? 0);
        $attended = (int)($result['attended'] ?? 0);
        $notAttended = (int)($result['not_attended'] ?? 0);
        $attendanceRate = $totalSessions > 0 ?
            round(($attended / $totalSessions) * 100, 1) : 0;

        return [
            'total_sessions' => $totalSessions,
            'attended' => $attended,
            'not_attended' => $notAttended,
            'attendance_rate' => $attendanceRate,
        ];
    }
}
