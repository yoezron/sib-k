<?php

/**
 * File Path: app/Controllers/HomeroomTeacher/ClassReportController.php
 * 
 * FINAL FIX - Verified dengan struktur database
 * Using raw SQL queries untuk akurasi 100%
 * 
 * @package    SIB-K
 * @version    FINAL-1.0
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

    public function index()
    {
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_wali_kelas()) {
            return redirect()->to('/')->with('error', 'Akses ditolak. Halaman ini hanya untuk Wali Kelas.');
        }

        $homeroomClass = $this->getHomeroomClass();

        if (!$homeroomClass) {
            return redirect()->to(route_to('homeroom.dashboard'))
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        $reportData = $this->getClassReport($homeroomClass['id']);

        $data = [
            'title' => 'Laporan Kelas - ' . $homeroomClass['class_name'],
            'pageTitle' => 'Laporan Kelas ' . $homeroomClass['class_name'],
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => route_to('homeroom.dashboard')],
                ['title' => 'Laporan Kelas', 'url' => '#', 'active' => true],
            ],
            'class' => $homeroomClass,
            'report' => $reportData,
        ];

        return view('homeroom_teacher/reports/class_summary', $data);
    }

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

    private function getClassReport($classId)
    {
        $report = [];
        $report['student_stats'] = $this->getStudentStatistics($classId);
        $report['gender_distribution'] = $this->getGenderDistribution($classId);
        $report['violation_stats'] = $this->getViolationStatistics($classId);
        $report['session_stats'] = $this->getSessionStatistics($classId);
        $report['top_violations'] = $this->getTopViolations($classId);
        $report['students_at_risk'] = $this->getStudentsAtRisk($classId);
        $report['recent_violations'] = $this->getRecentViolations($classId);
        $report['students'] = $this->getStudentsWithDetails($classId);
        $report['violation_trend'] = $this->getViolationTrend($classId);
        $report['session_attendance'] = $this->getSessionAttendance($classId);

        return $report;
    }

    private function getStudentStatistics($classId)
    {
        $total = $this->db->table('students')
            ->where('class_id', $classId)
            ->where('status', 'Aktif')
            ->where('deleted_at', null)
            ->countAllResults();

        $active = $total;

        $inactive = $this->db->table('students')
            ->where('class_id', $classId)
            ->whereIn('status', ['Tidak Aktif', 'Pindah', 'Lulus', 'Drop Out'])
            ->where('deleted_at', null)
            ->countAllResults();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
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

        $distribution = [
            'L' => 0,
            'P' => 0,
        ];

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

    private function getStudentsAtRisk($classId, $threshold = 50)
    {
        $query = "
            SELECT 
                s.id,
                s.nisn,
                s.full_name,
                s.gender,
                s.status,
                u.email,
                COALESCE(SUM(vc.points), 0) as violation_points,
                COUNT(v.id) as violation_count
            FROM students s
            INNER JOIN users u ON u.id = s.user_id
            LEFT JOIN violations v ON v.student_id = s.id AND v.deleted_at IS NULL
            LEFT JOIN violation_categories vc ON vc.id = v.category_id
            WHERE s.class_id = ?
              AND s.status = 'Aktif'
              AND s.deleted_at IS NULL
            GROUP BY s.id
            HAVING violation_points >= ?
            ORDER BY violation_points DESC
        ";

        return $this->db->query($query, [$classId, $threshold])->getResultArray();
    }

    private function getRecentViolations($classId, $days = 30)
    {
        $dateFrom = date('Y-m-d', strtotime("-{$days} days"));

        $query = "
            SELECT 
                v.*,
                u1.full_name as student_name,
                vc.category_name,
                vc.severity_level,
                u2.full_name as reporter_name
            FROM violations v
            INNER JOIN students s ON s.id = v.student_id
            INNER JOIN users u1 ON u1.id = s.user_id
            INNER JOIN violation_categories vc ON vc.id = v.category_id
            LEFT JOIN users u2 ON u2.id = v.reported_by
            WHERE s.class_id = ?
              AND s.status = 'Aktif'
              AND v.violation_date >= ?
              AND v.deleted_at IS NULL
            ORDER BY v.violation_date DESC, v.created_at DESC
            LIMIT 10
        ";

        return $this->db->query($query, [$classId, $dateFrom])->getResultArray();
    }

    private function getStudentsWithDetails($classId)
    {
        $query = "
            SELECT 
                s.id,
                s.nisn,
                s.full_name,
                s.gender,
                s.status,
                u.email,
                u.phone,
                COALESCE(SUM(vc.points), 0) as violation_points,
                COUNT(DISTINCT v.id) as violation_count,
                COUNT(DISTINCT cs.id) as session_count
            FROM students s
            INNER JOIN users u ON u.id = s.user_id
            LEFT JOIN violations v ON v.student_id = s.id AND v.deleted_at IS NULL
            LEFT JOIN violation_categories vc ON vc.id = v.category_id
            LEFT JOIN counseling_sessions cs ON cs.student_id = s.id AND cs.deleted_at IS NULL
            WHERE s.class_id = ?
              AND s.status = 'Aktif'
              AND s.deleted_at IS NULL
            GROUP BY s.id
            ORDER BY u.full_name ASC
        ";

        return $this->db->query($query, [$classId])->getResultArray();
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
        $attendanceRate = $totalSessions > 0 ? round(($attended / $totalSessions) * 100, 2) : 0;

        return [
            'total_sessions' => $totalSessions,
            'attended' => $attended,
            'not_attended' => $notAttended,
            'attendance_rate' => $attendanceRate,
        ];
    }

    public function exportPDF()
    {
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_wali_kelas()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $homeroomClass = $this->getHomeroomClass();

        if (!$homeroomClass) {
            return redirect()->to(route_to('homeroom.dashboard'))
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        $reportData = $this->getClassReport($homeroomClass['id']);

        $data = [
            'class' => $homeroomClass,
            'report' => $reportData,
            'generated_at' => date('d/m/Y H:i:s'),
            'generated_by' => auth_name(),
        ];

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

    public function exportExcel()
    {
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_wali_kelas()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $homeroomClass = $this->getHomeroomClass();

        if (!$homeroomClass) {
            return redirect()->to(route_to('homeroom.dashboard'))
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        $reportData = $this->getClassReport($homeroomClass['id']);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN KELAS');
        $sheet->setCellValue('A2', 'Kelas: ' . $homeroomClass['class_name']);
        $sheet->setCellValue('A3', 'Tahun Ajaran: ' . $homeroomClass['year_name']);
        $sheet->setCellValue('A4', 'Wali Kelas: ' . auth_name());
        $sheet->setCellValue('A5', 'Tanggal: ' . date('d/m/Y'));

        $row = 7;
        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'NISN');
        $sheet->setCellValue('C' . $row, 'Nama Lengkap');
        $sheet->setCellValue('D' . $row, 'L/P');
        $sheet->setCellValue('E' . $row, 'Poin Pelanggaran');
        $sheet->setCellValue('F' . $row, 'Jumlah Pelanggaran');
        $sheet->setCellValue('G' . $row, 'Jumlah Konseling');
        $sheet->setCellValue('H' . $row, 'Status');

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

    public function getReportData()
    {
        if (!is_logged_in() || !is_wali_kelas()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ]);
        }

        $homeroomClass = $this->getHomeroomClass();

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
}
