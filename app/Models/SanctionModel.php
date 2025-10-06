<?php

/**
 * File Path: app/Models/SanctionModel.php
 * 
 * Sanction Model
 * Model untuk mengelola data sanksi pelanggaran siswa
 * 
 * @package    SIB-K
 * @subpackage Models
 * @category   Model
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Models;

use CodeIgniter\Model;

class SanctionModel extends Model
{
    protected $table            = 'sanctions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'violation_id',
        'sanction_type',
        'sanction_date',
        'start_date',
        'end_date',
        'duration_days',
        'description',
        'status',
        'completed_date',
        'completion_notes',
        'assigned_by',
        'verified_by',
        'verified_at',
        'parent_acknowledged',
        'parent_acknowledged_at',
        'documents',
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
        'violation_id' => 'required|integer',
        'sanction_type' => 'required|max_length[100]',
        'sanction_date' => 'required|valid_date',
        'start_date' => 'permit_empty|valid_date',
        'end_date' => 'permit_empty|valid_date',
        'duration_days' => 'permit_empty|integer|greater_than_equal_to[0]',
        'description' => 'required|min_length[10]',
        'status' => 'permit_empty|in_list[Dijadwalkan,Sedang Berjalan,Selesai,Dibatalkan]',
        'assigned_by' => 'required|integer',
    ];

    protected $validationMessages = [
        'violation_id' => [
            'required' => 'ID pelanggaran harus diisi',
            'integer' => 'ID pelanggaran tidak valid',
        ],
        'sanction_type' => [
            'required' => 'Jenis sanksi harus diisi',
            'max_length' => 'Jenis sanksi maksimal 100 karakter',
        ],
        'sanction_date' => [
            'required' => 'Tanggal sanksi harus diisi',
            'valid_date' => 'Format tanggal tidak valid',
        ],
        'description' => [
            'required' => 'Deskripsi sanksi harus diisi',
            'min_length' => 'Deskripsi minimal 10 karakter',
        ],
        'assigned_by' => [
            'required' => 'Pemberi sanksi harus diisi',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['calculateDuration'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['calculateDuration'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Calculate duration in days from start_date to end_date
     * 
     * @param array $data
     * @return array
     */
    protected function calculateDuration(array $data)
    {
        if (isset($data['data']['start_date']) && isset($data['data']['end_date'])) {
            $startDate = new \DateTime($data['data']['start_date']);
            $endDate = new \DateTime($data['data']['end_date']);
            $interval = $startDate->diff($endDate);

            $data['data']['duration_days'] = $interval->days;
        }

        return $data;
    }

    /**
     * Get sanction with full details
     * 
     * @param int $id
     * @return array|null
     */
    public function getSanctionWithDetails($id)
    {
        return $this->select('sanctions.*,
                              violations.violation_date,
                              violations.description as violation_description,
                              students.nisn,
                              student_users.full_name as student_name,
                              classes.class_name,
                              violation_categories.category_name,
                              violation_categories.severity_level,
                              assigned_users.full_name as assigned_by_name,
                              verified_users.full_name as verified_by_name')
            ->join('violations', 'violations.id = sanctions.violation_id')
            ->join('students', 'students.id = violations.student_id')
            ->join('users as student_users', 'student_users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->join('users as assigned_users', 'assigned_users.id = sanctions.assigned_by')
            ->join('users as verified_users', 'verified_users.id = sanctions.verified_by', 'left')
            ->where('sanctions.id', $id)
            ->first();
    }

    /**
     * Get sanctions by violation
     * 
     * @param int $violationId
     * @return array
     */
    public function getByViolation($violationId)
    {
        return $this->select('sanctions.*,
                              assigned_users.full_name as assigned_by_name,
                              verified_users.full_name as verified_by_name')
            ->join('users as assigned_users', 'assigned_users.id = sanctions.assigned_by')
            ->join('users as verified_users', 'verified_users.id = sanctions.verified_by', 'left')
            ->where('sanctions.violation_id', $violationId)
            ->orderBy('sanctions.sanction_date', 'DESC')
            ->findAll();
    }

    /**
     * Get sanctions by student
     * 
     * @param int $studentId
     * @param int|null $limit
     * @return array
     */
    public function getByStudent($studentId, $limit = null)
    {
        $builder = $this->select('sanctions.*,
                                  violations.violation_date,
                                  violation_categories.category_name,
                                  violation_categories.severity_level')
            ->join('violations', 'violations.id = sanctions.violation_id')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('violations.student_id', $studentId)
            ->orderBy('sanctions.sanction_date', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get sanctions with filters
     * 
     * @param array $filters
     * @return array
     */
    public function getSanctionsWithFilters($filters = [])
    {
        $builder = $this->select('sanctions.*,
                                  violations.violation_date,
                                  students.nisn,
                                  student_users.full_name as student_name,
                                  classes.class_name,
                                  violation_categories.category_name,
                                  violation_categories.severity_level,
                                  assigned_users.full_name as assigned_by_name')
            ->join('violations', 'violations.id = sanctions.violation_id')
            ->join('students', 'students.id = violations.student_id')
            ->join('users as student_users', 'student_users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->join('users as assigned_users', 'assigned_users.id = sanctions.assigned_by');

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('sanctions.status', $filters['status']);
        }

        if (!empty($filters['sanction_type'])) {
            $builder->like('sanctions.sanction_type', $filters['sanction_type']);
        }

        if (!empty($filters['student_id'])) {
            $builder->where('violations.student_id', $filters['student_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('sanctions.sanction_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('sanctions.sanction_date <=', $filters['date_to']);
        }

        if (!empty($filters['assigned_by'])) {
            $builder->where('sanctions.assigned_by', $filters['assigned_by']);
        }

        if (!empty($filters['verified']) && $filters['verified'] === 'yes') {
            $builder->where('sanctions.verified_by IS NOT NULL', null, false);
        }

        if (!empty($filters['parent_acknowledged']) && $filters['parent_acknowledged'] === 'no') {
            $builder->where('sanctions.parent_acknowledged', 0);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('student_users.full_name', $filters['search'])
                ->orLike('students.nisn', $filters['search'])
                ->orLike('sanctions.sanction_type', $filters['search'])
                ->groupEnd();
        }

        // Order by date DESC
        $builder->orderBy('sanctions.sanction_date', 'DESC');

        return $builder->findAll();
    }

    /**
     * Get active sanctions (ongoing)
     * 
     * @param int|null $studentId
     * @return array
     */
    public function getActiveSanctions($studentId = null)
    {
        $builder = $this->select('sanctions.*,
                                  violations.violation_date,
                                  students.nisn,
                                  student_users.full_name as student_name,
                                  classes.class_name,
                                  violation_categories.category_name')
            ->join('violations', 'violations.id = sanctions.violation_id')
            ->join('students', 'students.id = violations.student_id')
            ->join('users as student_users', 'student_users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('sanctions.status', 'Sedang Berjalan');

        if ($studentId) {
            $builder->where('violations.student_id', $studentId);
        }

        return $builder->orderBy('sanctions.start_date', 'DESC')
            ->findAll();
    }

    /**
     * Get upcoming sanctions (scheduled)
     * 
     * @param int $limit
     * @return array
     */
    public function getUpcomingSanctions($limit = 10)
    {
        return $this->select('sanctions.*,
                              violations.violation_date,
                              students.nisn,
                              student_users.full_name as student_name,
                              classes.class_name,
                              violation_categories.category_name')
            ->join('violations', 'violations.id = sanctions.violation_id')
            ->join('students', 'students.id = violations.student_id')
            ->join('users as student_users', 'student_users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('sanctions.status', 'Dijadwalkan')
            ->where('sanctions.start_date >=', date('Y-m-d'))
            ->orderBy('sanctions.start_date', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Mark sanction as completed
     * 
     * @param int $id
     * @param string|null $notes
     * @return bool
     */
    public function markAsCompleted($id, $notes = null)
    {
        return $this->update($id, [
            'status' => 'Selesai',
            'completed_date' => date('Y-m-d'),
            'completion_notes' => $notes,
        ]);
    }

    /**
     * Mark parent as acknowledged
     * 
     * @param int $id
     * @return bool
     */
    public function markParentAcknowledged($id)
    {
        return $this->update($id, [
            'parent_acknowledged' => 1,
            'parent_acknowledged_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Verify sanction
     * 
     * @param int $id
     * @param int $verifiedBy
     * @return bool
     */
    public function verifySanction($id, $verifiedBy)
    {
        return $this->update($id, [
            'verified_by' => $verifiedBy,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get sanctions pending verification
     * 
     * @param int $limit
     * @return array
     */
    public function getPendingVerification($limit = 20)
    {
        return $this->select('sanctions.*,
                              violations.violation_date,
                              students.nisn,
                              student_users.full_name as student_name,
                              violation_categories.category_name,
                              violation_categories.severity_level,
                              assigned_users.full_name as assigned_by_name')
            ->join('violations', 'violations.id = sanctions.violation_id')
            ->join('students', 'students.id = violations.student_id')
            ->join('users as student_users', 'student_users.id = students.user_id')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->join('users as assigned_users', 'assigned_users.id = sanctions.assigned_by')
            ->where('sanctions.verified_by', null)
            ->where('sanctions.status !=', 'Dibatalkan')
            ->orderBy('sanctions.sanction_date', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get sanctions pending parent acknowledgement
     * 
     * @param int $limit
     * @return array
     */
    public function getPendingAcknowledgement($limit = 20)
    {
        return $this->select('sanctions.*,
                              violations.violation_date,
                              students.nisn,
                              student_users.full_name as student_name,
                              student_users.email as student_email,
                              violation_categories.category_name,
                              violation_categories.severity_level')
            ->join('violations', 'violations.id = sanctions.violation_id')
            ->join('students', 'students.id = violations.student_id')
            ->join('users as student_users', 'student_users.id = students.user_id')
            ->join('violation_categories', 'violation_categories.id = violations.category_id')
            ->where('sanctions.parent_acknowledged', 0)
            ->where('sanctions.status !=', 'Dibatalkan')
            ->orderBy('sanctions.sanction_date', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get statistics for sanctions
     * 
     * @param array $filters
     * @return array
     */
    public function getStatistics($filters = [])
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sanctions')
            ->select('COUNT(*) as total_sanctions,
                      SUM(CASE WHEN status = "Dijadwalkan" THEN 1 ELSE 0 END) as scheduled,
                      SUM(CASE WHEN status = "Sedang Berjalan" THEN 1 ELSE 0 END) as ongoing,
                      SUM(CASE WHEN status = "Selesai" THEN 1 ELSE 0 END) as completed,
                      SUM(CASE WHEN status = "Dibatalkan" THEN 1 ELSE 0 END) as cancelled,
                      SUM(CASE WHEN verified_by IS NULL THEN 1 ELSE 0 END) as pending_verification,
                      SUM(CASE WHEN parent_acknowledged = 0 THEN 1 ELSE 0 END) as pending_acknowledgement')
            ->where('deleted_at', null);

        // Apply date filter
        if (!empty($filters['date_from'])) {
            $builder->where('sanction_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('sanction_date <=', $filters['date_to']);
        }

        return $builder->get()->getRowArray();
    }

    /**
     * Get sanctions by type (statistics)
     * 
     * @param array $filters
     * @return array
     */
    public function getStatsBySanctionType($filters = [])
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sanctions')
            ->select('sanction_type,
                      COUNT(id) as sanction_count')
            ->where('deleted_at', null)
            ->groupBy('sanction_type')
            ->orderBy('sanction_count', 'DESC');

        // Apply date filter
        if (!empty($filters['date_from'])) {
            $builder->where('sanction_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('sanction_date <=', $filters['date_to']);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Auto-update sanction status based on dates
     * Should be run via cron job or scheduler
     * 
     * @return array [updated_count, details]
     */
    public function autoUpdateStatus()
    {
        $today = date('Y-m-d');
        $updated = 0;

        // Update scheduled to ongoing
        $scheduled = $this->where('status', 'Dijadwalkan')
            ->where('start_date <=', $today)
            ->where('start_date IS NOT NULL', null, false)
            ->findAll();

        foreach ($scheduled as $sanction) {
            $this->update($sanction['id'], ['status' => 'Sedang Berjalan']);
            $updated++;
        }

        // Update ongoing to completed
        $ongoing = $this->where('status', 'Sedang Berjalan')
            ->where('end_date <=', $today)
            ->where('end_date IS NOT NULL', null, false)
            ->findAll();

        foreach ($ongoing as $sanction) {
            $this->markAsCompleted($sanction['id'], 'Auto-completed by system based on end date');
            $updated++;
        }

        return [
            'updated_count' => $updated,
            'scheduled_to_ongoing' => count($scheduled),
            'ongoing_to_completed' => count($ongoing),
        ];
    }

    /**
     * Get common sanction types for dropdown
     * 
     * @return array
     */
    public function getCommonSanctionTypes()
    {
        return [
            'Teguran Lisan',
            'Teguran Tertulis',
            'Pemanggilan Orang Tua',
            'Pembinaan Khusus',
            'Skorsing 1 Hari',
            'Skorsing 3 Hari',
            'Skorsing 1 Minggu',
            'Kerja Sosial',
            'Poin Pengurangan',
            'Pembuatan Surat Pernyataan',
            'Konseling Wajib',
        ];
    }
}
