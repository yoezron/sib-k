<?php

/**
 * File Path: app/Services/AcademicYearService.php
 * 
 * Academic Year Service
 * Business logic layer untuk Academic Year management
 * 
 * @package    SIB-K
 * @subpackage Services
 * @category   Business Logic
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Services;

use App\Models\AcademicYearModel;
use App\Models\ClassModel;
use App\Validation\AcademicYearValidation;
use CodeIgniter\Database\Exceptions\DatabaseException;

class AcademicYearService
{
    protected $academicYearModel;
    protected $classModel;
    protected $db;

    public function __construct()
    {
        $this->academicYearModel = new AcademicYearModel();
        $this->classModel = new ClassModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get all academic years with filter and pagination
     * 
     * @param array $filters
     * @param int $perPage
     * @return array
     */
    public function getAllAcademicYears($filters = [], $perPage = 10)
    {
        $builder = $this->academicYearModel
            ->select('academic_years.*,
                      (SELECT COUNT(*) FROM classes WHERE classes.academic_year_id = academic_years.id AND classes.deleted_at IS NULL) as class_count');

        // Apply filters
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder->where('academic_years.is_active', $filters['is_active']);
        }

        if (!empty($filters['semester'])) {
            $builder->where('academic_years.semester', $filters['semester']);
        }

        if (!empty($filters['search'])) {
            $builder->like('academic_years.year_name', $filters['search']);
        }

        // Order by
        $orderBy = $filters['order_by'] ?? 'academic_years.year_name';
        $orderDir = $filters['order_dir'] ?? 'DESC';

        $builder->orderBy($orderBy, $orderDir);

        // Get paginated results
        $academicYears = $builder->paginate($perPage);
        $pager = $this->academicYearModel->pager;

        return [
            'academic_years' => $academicYears,
            'pager' => $pager,
            'total' => $pager->getTotal(),
            'per_page' => $perPage,
            'current_page' => $pager->getCurrentPage(),
            'last_page' => $pager->getPageCount(),
        ];
    }

    /**
     * Get academic year by ID with details
     * 
     * @param int $id
     * @return array|null
     */
    public function getAcademicYearById($id)
    {
        $year = $this->academicYearModel->find($id);

        if (!$year) {
            return null;
        }

        // Get class count
        $year['class_count'] = $this->classModel
            ->where('academic_year_id', $id)
            ->countAllResults();

        // Get classes detail
        $year['classes'] = $this->classModel
            ->select('classes.*, COUNT(students.id) as student_count')
            ->join('students', 'students.class_id = classes.id AND students.status = "Aktif" AND students.deleted_at IS NULL', 'left')
            ->where('classes.academic_year_id', $id)
            ->groupBy('classes.id')
            ->findAll();

        // Calculate duration
        $year['duration_days'] = AcademicYearValidation::getDuration($year['start_date'], $year['end_date']);

        return $year;
    }

    /**
     * Create new academic year
     * 
     * @param array $data
     * @return array ['success' => bool, 'message' => string, 'year_id' => int|null]
     */
    public function createAcademicYear($data)
    {
        try {
            // Sanitize input
            $data = AcademicYearValidation::sanitizeInput($data);

            // Validate year name format
            $yearNameCheck = AcademicYearValidation::validateYearName($data['year_name']);
            if (!$yearNameCheck['valid']) {
                return [
                    'success' => false,
                    'message' => $yearNameCheck['message'],
                ];
            }

            // Validate date range
            $dateRangeCheck = AcademicYearValidation::validateDateRange($data['start_date'], $data['end_date']);
            if (!$dateRangeCheck['valid']) {
                return [
                    'success' => false,
                    'message' => $dateRangeCheck['message'],
                ];
            }

            // Start transaction
            $this->db->transStart();

            // If set as active, deactivate others first
            if (!empty($data['is_active']) && $data['is_active'] == 1) {
                $this->deactivateAllAcademicYears();
            }

            // Insert academic year
            if (!$this->academicYearModel->insert($data)) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal membuat tahun ajaran: ' . implode(', ', $this->academicYearModel->errors()),
                ];
            }

            $yearId = $this->academicYearModel->getInsertID();

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data',
                ];
            }

            // Log activity
            $this->logActivity('create', $yearId, "Tahun ajaran {$data['year_name']} berhasil dibuat");

            return [
                'success' => true,
                'message' => 'Tahun ajaran berhasil dibuat',
                'year_id' => $yearId,
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creating academic year: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update academic year
     * 
     * @param int $id
     * @param array $data
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateAcademicYear($id, $data)
    {
        try {
            // Check if academic year exists
            $year = $this->academicYearModel->find($id);
            if (!$year) {
                return [
                    'success' => false,
                    'message' => 'Tahun ajaran tidak ditemukan',
                ];
            }

            // Sanitize input
            $data = AcademicYearValidation::sanitizeInput($data);

            // Validate year name format
            $yearNameCheck = AcademicYearValidation::validateYearName($data['year_name']);
            if (!$yearNameCheck['valid']) {
                return [
                    'success' => false,
                    'message' => $yearNameCheck['message'],
                ];
            }

            // Validate date range
            $dateRangeCheck = AcademicYearValidation::validateDateRange($data['start_date'], $data['end_date']);
            if (!$dateRangeCheck['valid']) {
                return [
                    'success' => false,
                    'message' => $dateRangeCheck['message'],
                ];
            }

            // Start transaction
            $this->db->transStart();

            // If set as active, deactivate others first
            if (!empty($data['is_active']) && $data['is_active'] == 1 && $year['is_active'] != 1) {
                $this->deactivateAllAcademicYears($id);
            }

            // Update academic year
            if (!$this->academicYearModel->update($id, $data)) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal mengupdate tahun ajaran: ' . implode(', ', $this->academicYearModel->errors()),
                ];
            }

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data',
                ];
            }

            // Log activity
            $this->logActivity('update', $id, "Tahun ajaran {$data['year_name']} berhasil diupdate");

            return [
                'success' => true,
                'message' => 'Tahun ajaran berhasil diupdate',
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error updating academic year: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete academic year
     * 
     * @param int $id
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteAcademicYear($id)
    {
        try {
            // Check if academic year exists
            $year = $this->academicYearModel->find($id);
            if (!$year) {
                return [
                    'success' => false,
                    'message' => 'Tahun ajaran tidak ditemukan',
                ];
            }

            // Check if can be deleted
            $canDeleteCheck = AcademicYearValidation::canDelete($id);
            if (!$canDeleteCheck['can_delete']) {
                return [
                    'success' => false,
                    'message' => $canDeleteCheck['message'],
                ];
            }

            // Start transaction
            $this->db->transStart();

            // Soft delete academic year
            if (!$this->academicYearModel->delete($id)) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus tahun ajaran',
                ];
            }

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data',
                ];
            }

            // Log activity
            $this->logActivity('delete', $id, "Tahun ajaran {$year['year_name']} berhasil dihapus");

            return [
                'success' => true,
                'message' => 'Tahun ajaran berhasil dihapus',
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error deleting academic year: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Set academic year as active (deactivate others)
     * 
     * @param int $id
     * @return array ['success' => bool, 'message' => string]
     */
    public function setActiveAcademicYear($id)
    {
        try {
            // Check if academic year exists
            $year = $this->academicYearModel->find($id);
            if (!$year) {
                return [
                    'success' => false,
                    'message' => 'Tahun ajaran tidak ditemukan',
                ];
            }

            // If already active, no need to do anything
            if ($year['is_active'] == 1) {
                return [
                    'success' => true,
                    'message' => 'Tahun ajaran sudah aktif',
                ];
            }

            // Start transaction
            $this->db->transStart();

            // Deactivate all academic years
            $this->deactivateAllAcademicYears();

            // Activate this academic year
            if (!$this->academicYearModel->update($id, ['is_active' => 1])) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal mengaktifkan tahun ajaran',
                ];
            }

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data',
                ];
            }

            // Log activity
            $this->logActivity('set_active', $id, "Tahun ajaran {$year['year_name']} diset sebagai aktif");

            return [
                'success' => true,
                'message' => "Tahun ajaran {$year['year_name']} berhasil diaktifkan",
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error setting active academic year: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get active academic year
     * 
     * @return array|null
     */
    public function getActiveAcademicYear()
    {
        $year = $this->academicYearModel
            ->where('is_active', 1)
            ->first();

        if (!$year) {
            return null;
        }

        // Get class count
        $year['class_count'] = $this->classModel
            ->where('academic_year_id', $year['id'])
            ->countAllResults();

        return $year;
    }

    /**
     * Get academic year statistics
     * 
     * @return array
     */
    public function getAcademicYearStatistics()
    {
        $stats = [
            'total' => $this->academicYearModel->countAllResults(false),
            'active' => $this->academicYearModel->where('is_active', 1)->countAllResults(false),
            'by_semester' => [],
        ];

        // Get count by semester
        $semesterStats = $this->db->table('academic_years')
            ->select('semester, COUNT(id) as total')
            ->where('deleted_at', null)
            ->groupBy('semester')
            ->get()
            ->getResultArray();

        foreach ($semesterStats as $stat) {
            $stats['by_semester'][$stat['semester']] = (int)$stat['total'];
        }

        return $stats;
    }

    /**
     * Deactivate all academic years
     * 
     * @param int|null $excludeId Exclude this ID from deactivation
     * @return bool
     */
    protected function deactivateAllAcademicYears($excludeId = null)
    {
        $builder = $this->db->table('academic_years')
            ->set('is_active', 0);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->update();
    }

    /**
     * Check if academic year overlaps with existing years
     * 
     * @param string $startDate
     * @param string $endDate
     * @param int|null $excludeId
     * @return array ['overlaps' => bool, 'conflicting_years' => array]
     */
    public function checkOverlap($startDate, $endDate, $excludeId = null)
    {
        $builder = $this->academicYearModel
            ->where("(
                (start_date <= '{$startDate}' AND end_date >= '{$startDate}') OR
                (start_date <= '{$endDate}' AND end_date >= '{$endDate}') OR
                (start_date >= '{$startDate}' AND end_date <= '{$endDate}')
            )");

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        $conflictingYears = $builder->findAll();

        return [
            'overlaps' => !empty($conflictingYears),
            'conflicting_years' => $conflictingYears,
        ];
    }

    /**
     * Get suggested academic year based on latest
     * 
     * @return array ['year_name' => string, 'semester' => string, 'start_date' => string, 'end_date' => string]
     */
    public function getSuggestedAcademicYear()
    {
        // Get latest academic year
        $latest = $this->academicYearModel
            ->orderBy('year_name', 'DESC')
            ->first();

        if (!$latest) {
            // No previous data, suggest current academic year
            $currentMonth = (int)date('m');
            $semester = ($currentMonth >= 7) ? 'Ganjil' : 'Genap';
            $yearName = AcademicYearValidation::generateYearName(date('Y-m-d'));
            $dateRange = AcademicYearValidation::getDefaultDateRange($semester);

            return [
                'year_name' => $yearName,
                'semester' => $semester,
                'start_date' => $dateRange['start_date'],
                'end_date' => $dateRange['end_date'],
            ];
        }

        // Suggest next semester
        $parsed = AcademicYearValidation::parseYearName($latest['year_name']);

        if ($latest['semester'] === 'Ganjil') {
            // Next is Genap with same year
            $semester = 'Genap';
            $yearName = $latest['year_name'];
            $dateRange = AcademicYearValidation::getDefaultDateRange('Genap');
            // Adjust year for Genap
            $dateRange['start_date'] = $parsed['year2'] . '-01-01';
            $dateRange['end_date'] = $parsed['year2'] . '-06-30';
        } else {
            // Next is Ganjil with next year
            $semester = 'Ganjil';
            $yearName = $parsed['year2'] . '/' . ($parsed['year2'] + 1);
            $dateRange['start_date'] = $parsed['year2'] . '-07-01';
            $dateRange['end_date'] = $parsed['year2'] . '-12-31';
        }

        return [
            'year_name' => $yearName,
            'semester' => $semester,
            'start_date' => $dateRange['start_date'],
            'end_date' => $dateRange['end_date'],
        ];
    }

    /**
     * Log academic year activity
     * 
     * @param string $action
     * @param int $yearId
     * @param string $description
     * @return void
     */
    private function logActivity($action, $yearId, $description)
    {
        log_message('info', "[AcademicYearService] Action: {$action}, Year ID: {$yearId}, Description: {$description}");
    }
}
