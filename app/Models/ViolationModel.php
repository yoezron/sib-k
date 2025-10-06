<?php

/**
 * File Path: app/Models/ViolationModel.php
 * 
 * Violation Model
 * Model untuk mengelola data pelanggaran siswa
 * 
 * @package    SIB-K
 * @subpackage Models
 * @category   Model
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Models;

use CodeIgniter\Model;

class ViolationModel extends Model
{
    protected $table            = 'violations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'student_id',
        'category_id',
        'violation_date',
        'violation_time',
        'location',
        'description',
        'evidence',
        'reported_by',
        'handled_by',
        'status',
        'resolution_notes',
        'resolution_date',
        'parent_notified',
        'parent_notified_at',
        'is_repeat_offender',
        'notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'student_id' => 'required|integer',
        'category_id' => 'required|integer',
        'violation_date' => 'required|valid_date',
        'violation_time' => 'permit_empty|valid_time',
        'location' => 'permit_empty|max_length[200]',
        'description' => 'required|min_length[10]',
        'reported_by' => 'required|integer',
        'status' => 'permit_empty|in_list[Dilaporkan,Dalam Proses,Selesai,Dibatalkan]',
    ];

    protected $validationMessages = [
        'student_id' => [
            'required' => 'Siswa harus dipilih',
            'integer' => 'ID siswa tidak valid',
        ],
        'category_id' => [
            'required' => 'Kategori pelanggaran harus dipilih',
            'integer' => 'ID kategori tidak valid',
        ],
        'violation_date' => [
            'required' => 'Tanggal pelanggaran harus diisi',
            'valid_date' => 'Format tanggal tidak valid',
        ],
        'description' => [
            'required' => 'Deskripsi pelanggaran harus diisi',
            'min_length' => 'Deskripsi minimal 10 karakter',
        ],
        'reported_by' => [
            'required' => 'Pelapor harus diisi',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['checkRepeatOffender'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Check if student is repeat offender
     * 
     * @param array $data
     * @return array
     */
    protected function checkRepeatOffender(array $data)
    {
        if (isset($data['data']['student_id'])) {
            $studentId = $data['data']['student_id'];

            // Check violations in last 3 months
            $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));

            $violationCount = $this->where('student_id', $studentId)
                ->where('violation_date >=', $threeMonthsAgo)
                ->where('status !=', 'Dibatalkan')
                ->countAllResults(false);

            // If more than 3 violations in 3 months, mark as repeat offender
            if ($violationCount >= 3) {
                $data['data']['is_repeat_offender'] = 1;
            }
        }

        return $data;
    }

    /**
     * Get violation with full details (joins)
     * 
     * @param int $id
     * @return array|null
     */
    public function getViolationWithDetails($id)
    {
        return $this->select('violations.*,
                              students.nisn, students.nis,
                              student_users.full_name as student_name,
                              student_users.email as student_email,
                              classes.class_name,
                              violation_categories.category_name,
                              violation_categories.severity_level,
                              violation_categories.point_deduction,
                              reporter_users.full_name as reporter_name,
                              reporter_users.email as reporter_email,
                              handler_users.full_name as handler_name,
                              handler_users.email as handler_email,
                              (SELECT COUNT(*) FROM sanctions 
                               WHERE sanctions.violation_id = violations.id 
                               AND sanctions.deleted_at IS NULL) as sanction_count')
            ->join('students', 'students.id = violations.student_id')
            ->join('users as student_users', 'student_users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->join('users as reporter_users', 'reporter_users.id = violations.reported_by')
            ->join('users as handler_users', 'handler_users.id = violations.handled_by', 'left')
            ->where('violations.id', $id)
            ->first();
    }

    /**
     * Get violations with filters
     * 
     * @param array $filters
     * @return array
     */
    public function getViolationsWithFilters($filters = [])
    {
        $builder = $this->select('violations.*,
                                  students.nisn,
                                  student_users.full_name as student_name,
                                  classes.class_name,
                                  violation_categories.category_name,
                                  violation_categories.severity_level,
                                  violation_categories.point_deduction,
                                  reporter_users.full_name as reporter_name,
                                  handler_users.full_name as handler_name')
            ->join('students', 'students.id = violations.student_id')
            ->join('users as student_users', 'student_users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->join('users as reporter_users', 'reporter_users.id = violations.reported_by')
            ->join('users as handler_users', 'handler_users.id = violations.handled_by', 'left');

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('violations.status', $filters['status']);
        }

        if (!empty($filters['severity_level'])) {
            $builder->where('violation_categories.severity_level', $filters['severity_level']);
        }

        if (!empty($filters['student_id'])) {
            $builder->where('violations.student_id', $filters['student_id']);
        }

        if (!empty($filters['category_id'])) {
            $builder->where('violations.category_id', $filters['category_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('violations.violation_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('violations.violation_date <=', $filters['date_to']);
        }

        if (!empty($filters['handled_by'])) {
            $builder->where('violations.handled_by', $filters['handled_by']);
        }

        if (!empty($filters['is_repeat_offender'])) {
            $builder->where('violations.is_repeat_offender', 1);
        }

        if (!empty($filters['parent_notified']) && $filters['parent_notified'] === 'no') {
            $builder->where('violations.parent_notified', 0);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('student_users.full_name', $filters['search'])
                ->orLike('students.nisn', $filters['search'])
                ->orLike('violations.description', $filters['search'])
                ->groupEnd();
        }

        // Order by date DESC
        $builder->orderBy('violations.violation_date', 'DESC');
        $builder->orderBy('violations.created_at', 'DESC');

        return $builder->findAll();
    }

    /**
     * Get violations by student
     * 
     * @param int $studentId
     * @param int|null $limit
     * @return array
     */
    public function getByStudent($studentId, $limit = null)
    {
        $builder = $this->select('violations.*,
                                  violation_categories.category_name,
                                  violation_categories.severity_level,
                                  violation_categories.point_deduction')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('violations.student_id', $studentId)
            ->orderBy('violations.violation_date', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get statistics for violations
     * 
     * @param array $filters
     * @return array
     */
    public function getStatistics($filters = [])
    {
        $db = \Config\Database::connect();

        $builder = $db->table('violations')
            ->select('COUNT(*) as total_violations,
                      SUM(CASE WHEN status = "Dilaporkan" THEN 1 ELSE 0 END) as reported,
                      SUM(CASE WHEN status = "Dalam Proses" THEN 1 ELSE 0 END) as in_process,
                      SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) as completed,
                      SUM(CASE WHEN status = "Dibatalkan" THEN 1 ELSE 0 END) as cancelled,
                      SUM(CASE WHEN is_repeat_offender = 1 THEN 1 ELSE 0 END) as repeat_offenders,
                      SUM(CASE WHEN parent_notified = 0 THEN 1 ELSE 0 END) as parents_not_notified')
            ->where('deleted_at', null);

        // Apply date filter
        if (!empty($filters['date_from'])) {
            $builder->where('violation_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('violation_date <=', $filters['date_to']);
        }

        return $builder->get()->getRowArray();
    }

    /**
     * Get violations by severity level (statistics)
     * 
     * @param array $filters
     * @return array
     */
    public function getStatsBySeverity($filters = [])
    {
        $db = \Config\Database::connect();

        $builder = $db->table('violations')
            ->select('violation_categories.severity_level,
                      COUNT(violations.id) as violation_count,
                      SUM(violation_categories.point_deduction) as total_points')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('violations.deleted_at', null)
            ->groupBy('violation_categories.severity_level');

        // Apply date filter
        if (!empty($filters['date_from'])) {
            $builder->where('violations.violation_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('violations.violation_date <=', $filters['date_to']);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get top violators (students with most violations)
     * 
     * @param int $limit
     * @param array $filters
     * @return array
     */
    public function getTopViolators($limit = 10, $filters = [])
    {
        $db = \Config\Database::connect();

        $builder = $db->table('violations')
            ->select('students.id,
                      students.nisn,
                      users.full_name as student_name,
                      classes.class_name,
                      COUNT(violations.id) as violation_count,
                      SUM(violation_categories.point_deduction) as total_points,
                      MAX(violations.violation_date) as last_violation_date')
            ->join('students', 'students.id = violations.student_id')
            ->join('users', 'users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('violations.deleted_at', null)
            ->where('violations.status !=', 'Dibatalkan')
            ->groupBy('students.id')
            ->orderBy('violation_count', 'DESC')
            ->limit($limit);

        // Apply date filter
        if (!empty($filters['date_from'])) {
            $builder->where('violations.violation_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('violations.violation_date <=', $filters['date_to']);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mark parent as notified
     * 
     * @param int $id
     * @return bool
     */
    public function markParentNotified($id)
    {
        return $this->update($id, [
            'parent_notified' => 1,
            'parent_notified_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get pending notifications (parent not notified)
     * 
     * @param int $limit
     * @return array
     */
    public function getPendingNotifications($limit = 20)
    {
        return $this->select('violations.*,
                              students.nisn,
                              student_users.full_name as student_name,
                              student_users.email as student_email,
                              classes.class_name,
                              violation_categories.category_name,
                              violation_categories.severity_level')
            ->join('students', 'students.id = violations.student_id')
            ->join('users as student_users', 'student_users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('violations.parent_notified', 0)
            ->where('violations.status !=', 'Dibatalkan')
            ->orderBy('violations.violation_date', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get student total points from violations
     * 
     * @param int $studentId
     * @param array $filters (optional date range)
     * @return int
     */
    public function getStudentTotalPoints($studentId, $filters = [])
    {
        $db = \Config\Database::connect();

        $builder = $db->table('violations')
            ->select('SUM(violation_categories.point_deduction) as total_points')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('violations.student_id', $studentId)
            ->where('violations.deleted_at', null)
            ->where('violations.status !=', 'Dibatalkan');

        // Apply date filter if provided
        if (!empty($filters['date_from'])) {
            $builder->where('violations.violation_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('violations.violation_date <=', $filters['date_to']);
        }

        $result = $builder->get()->getRowArray();

        return (int) ($result['total_points'] ?? 0);
    }

    /**
     * Get violations for monthly report
     * 
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getMonthlyViolations($year, $month)
    {
        $startDate = date('Y-m-01', strtotime("$year-$month-01"));
        $endDate = date('Y-m-t', strtotime("$year-$month-01"));

        return $this->getViolationsWithFilters([
            'date_from' => $startDate,
            'date_to' => $endDate,
        ]);
    }
}
