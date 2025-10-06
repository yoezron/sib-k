<?php

/**
 * File Path: app/Models/SessionParticipantModel.php
 * 
 * Session Participant Model
 * Mengelola peserta sesi konseling kelompok atau klasikal
 * 
 * @package    SIB-K
 * @subpackage Models
 * @category   Counseling
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Models;

use CodeIgniter\Model;

class SessionParticipantModel extends Model
{
    protected $table            = 'session_participants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'session_id',
        'student_id',
        'attendance_status',
        'participation_note',
        'is_active',
        'joined_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'session_id'        => 'required|integer|is_not_unique[counseling_sessions.id]',
        'student_id'        => 'required|integer|is_not_unique[students.id]',
        'attendance_status' => 'permit_empty|in_list[Hadir,Tidak Hadir,Izin,Sakit]',
        'is_active'         => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'session_id' => [
            'required'       => 'Sesi konseling harus dipilih',
            'is_not_unique'  => 'Sesi konseling tidak valid',
        ],
        'student_id' => [
            'required'       => 'Siswa harus dipilih',
            'is_not_unique'  => 'Siswa tidak valid',
        ],
        'attendance_status' => [
            'in_list' => 'Status kehadiran tidak valid',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setDefaults'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Set default values before insert
     * 
     * @param array $data
     * @return array
     */
    protected function setDefaults(array $data)
    {
        if (!isset($data['data']['attendance_status'])) {
            $data['data']['attendance_status'] = 'Hadir';
        }

        if (!isset($data['data']['is_active'])) {
            $data['data']['is_active'] = 1;
        }

        if (!isset($data['data']['joined_at'])) {
            $data['data']['joined_at'] = date('Y-m-d H:i:s');
        }

        return $data;
    }

    /**
     * Get participants by session with student details
     * 
     * @param int $sessionId
     * @param bool $activeOnly
     * @return array
     */
    public function getParticipantsBySession($sessionId, $activeOnly = true)
    {
        $builder = $this->select('session_participants.*,
                                  students.nisn, students.nis, students.gender,
                                  users.full_name as student_name,
                                  users.email as student_email,
                                  classes.class_name')
            ->join('students', 'students.id = session_participants.student_id')
            ->join('users', 'users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('session_participants.session_id', $sessionId);

        if ($activeOnly) {
            $builder->where('session_participants.is_active', 1);
        }

        return $builder->orderBy('users.full_name', 'ASC')->findAll();
    }

    /**
     * Get sessions by student participant
     * 
     * @param int $studentId
     * @param int|null $limit
     * @return array
     */
    public function getSessionsByStudent($studentId, $limit = null)
    {
        $builder = $this->select('session_participants.*,
                                  counseling_sessions.topic,
                                  counseling_sessions.session_date,
                                  counseling_sessions.session_type,
                                  counseling_sessions.status,
                                  users.full_name as counselor_name')
            ->join('counseling_sessions', 'counseling_sessions.id = session_participants.session_id')
            ->join('users', 'users.id = counseling_sessions.counselor_id')
            ->where('session_participants.student_id', $studentId)
            ->orderBy('counseling_sessions.session_date', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Add multiple participants to session
     * 
     * @param int $sessionId
     * @param array $studentIds
     * @return array ['success' => int, 'failed' => int, 'errors' => array]
     */
    public function addMultipleParticipants($sessionId, $studentIds)
    {
        $result = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($studentIds as $studentId) {
            // Check if already exists
            $exists = $this->where('session_id', $sessionId)
                ->where('student_id', $studentId)
                ->first();

            if ($exists) {
                $result['failed']++;
                $result['errors'][] = "Siswa ID {$studentId} sudah terdaftar";
                continue;
            }

            $data = [
                'session_id' => $sessionId,
                'student_id' => $studentId,
                'attendance_status' => 'Hadir',
                'is_active' => 1,
                'joined_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->insert($data)) {
                $result['success']++;
            } else {
                $result['failed']++;
                $result['errors'][] = "Gagal menambahkan siswa ID {$studentId}";
            }
        }

        return $result;
    }

    /**
     * Update attendance status
     * 
     * @param int $participantId
     * @param string $status
     * @return bool
     */
    public function updateAttendance($participantId, $status)
    {
        $validStatuses = ['Hadir', 'Tidak Hadir', 'Izin', 'Sakit'];

        if (!in_array($status, $validStatuses)) {
            return false;
        }

        return $this->update($participantId, [
            'attendance_status' => $status
        ]);
    }

    /**
     * Update attendance for multiple participants
     * 
     * @param array $attendanceData ['participant_id' => 'status', ...]
     * @return array ['success' => int, 'failed' => int]
     */
    public function updateMultipleAttendance($attendanceData)
    {
        $result = [
            'success' => 0,
            'failed' => 0,
        ];

        foreach ($attendanceData as $participantId => $status) {
            if ($this->updateAttendance($participantId, $status)) {
                $result['success']++;
            } else {
                $result['failed']++;
            }
        }

        return $result;
    }

    /**
     * Remove participant from session (soft)
     * 
     * @param int $participantId
     * @return bool
     */
    public function removeParticipant($participantId)
    {
        return $this->update($participantId, [
            'is_active' => 0
        ]);
    }

    /**
     * Get attendance statistics for session
     * 
     * @param int $sessionId
     * @return array
     */
    public function getAttendanceStats($sessionId)
    {
        $participants = $this->where('session_id', $sessionId)
            ->where('is_active', 1)
            ->findAll();

        $stats = [
            'total' => count($participants),
            'hadir' => 0,
            'tidak_hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
        ];

        foreach ($participants as $participant) {
            switch ($participant['attendance_status']) {
                case 'Hadir':
                    $stats['hadir']++;
                    break;
                case 'Tidak Hadir':
                    $stats['tidak_hadir']++;
                    break;
                case 'Izin':
                    $stats['izin']++;
                    break;
                case 'Sakit':
                    $stats['sakit']++;
                    break;
            }
        }

        $stats['attendance_rate'] = $stats['total'] > 0
            ? round(($stats['hadir'] / $stats['total']) * 100, 2)
            : 0;

        return $stats;
    }

    /**
     * Get participant count by session
     * 
     * @param int $sessionId
     * @param bool $activeOnly
     * @return int
     */
    public function getParticipantCount($sessionId, $activeOnly = true)
    {
        $builder = $this->where('session_id', $sessionId);

        if ($activeOnly) {
            $builder->where('is_active', 1);
        }

        return $builder->countAllResults();
    }

    /**
     * Check if student is participant in session
     * 
     * @param int $sessionId
     * @param int $studentId
     * @return bool
     */
    public function isParticipant($sessionId, $studentId)
    {
        return $this->where('session_id', $sessionId)
            ->where('student_id', $studentId)
            ->where('is_active', 1)
            ->countAllResults() > 0;
    }

    /**
     * Get active participants from class
     * 
     * @param int $sessionId
     * @param int $classId
     * @return array
     */
    public function getParticipantsFromClass($sessionId, $classId)
    {
        return $this->select('session_participants.*,
                              students.nisn, students.nis,
                              users.full_name as student_name')
            ->join('students', 'students.id = session_participants.student_id')
            ->join('users', 'users.id = students.user_id')
            ->where('session_participants.session_id', $sessionId)
            ->where('students.class_id', $classId)
            ->where('session_participants.is_active', 1)
            ->findAll();
    }

    /**
     * Get participation history for student
     * 
     * @param int $studentId
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     */
    public function getParticipationHistory($studentId, $dateFrom = null, $dateTo = null)
    {
        $builder = $this->select('session_participants.*,
                                  counseling_sessions.topic,
                                  counseling_sessions.session_date,
                                  counseling_sessions.session_type,
                                  users.full_name as counselor_name')
            ->join('counseling_sessions', 'counseling_sessions.id = session_participants.session_id')
            ->join('users', 'users.id = counseling_sessions.counselor_id')
            ->where('session_participants.student_id', $studentId);

        if ($dateFrom) {
            $builder->where('counseling_sessions.session_date >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('counseling_sessions.session_date <=', $dateTo);
        }

        return $builder->orderBy('counseling_sessions.session_date', 'DESC')->findAll();
    }

    /**
     * Get students not yet in session (from specific class)
     * 
     * @param int $sessionId
     * @param int $classId
     * @return array
     */
    public function getStudentsNotInSession($sessionId, $classId)
    {
        $db = \Config\Database::connect();

        // Get students already in session
        $participantIds = $this->select('student_id')
            ->where('session_id', $sessionId)
            ->where('is_active', 1)
            ->findColumn('student_id');

        // Get all students from class not in session
        $builder = $db->table('students')
            ->select('students.id, students.nisn, students.nis, users.full_name as student_name')
            ->join('users', 'users.id = students.user_id')
            ->where('students.class_id', $classId)
            ->where('students.status', 'Aktif')
            ->where('students.deleted_at', null);

        if (!empty($participantIds)) {
            $builder->whereNotIn('students.id', $participantIds);
        }

        return $builder->orderBy('users.full_name', 'ASC')->get()->getResultArray();
    }
}
