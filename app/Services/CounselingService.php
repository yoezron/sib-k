<?php

/**
 * File Path: app/Services/CounselingService.php
 * 
 * Counseling Service
 * Business logic layer untuk Counseling Session management
 * 
 * @package    SIB-K
 * @subpackage Services
 * @category   Business Logic
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Services;

use App\Models\CounselingSessionModel;
use App\Models\SessionNoteModel;
use App\Models\SessionParticipantModel;
use App\Models\StudentModel;
use App\Models\ClassModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class CounselingService
{
    protected $sessionModel;
    protected $noteModel;
    protected $participantModel;
    protected $studentModel;
    protected $classModel;
    protected $db;

    public function __construct()
    {
        $this->sessionModel = new CounselingSessionModel();
        $this->noteModel = new SessionNoteModel();
        $this->participantModel = new SessionParticipantModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get counselor dashboard statistics
     * 
     * @param int $counselorId
     * @return array
     */
    public function getDashboardStats($counselorId)
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');

        $stats = [
            // Total sessions
            'total_sessions' => $this->sessionModel
                ->where('counselor_id', $counselorId)
                ->countAllResults(false),

            // Sessions today
            'sessions_today' => $this->sessionModel
                ->where('counselor_id', $counselorId)
                ->where('session_date', $today)
                ->where('status !=', 'Dibatalkan')
                ->countAllResults(false),

            // Sessions this month
            'sessions_this_month' => $this->sessionModel
                ->where('counselor_id', $counselorId)
                ->like('session_date', $thisMonth)
                ->where('status !=', 'Dibatalkan')
                ->countAllResults(false),

            // Upcoming sessions
            'upcoming_sessions' => $this->sessionModel
                ->where('counselor_id', $counselorId)
                ->where('session_date >=', $today)
                ->where('status', 'Dijadwalkan')
                ->countAllResults(false),

            // Completed sessions
            'completed_sessions' => $this->sessionModel
                ->where('counselor_id', $counselorId)
                ->where('status', 'Selesai')
                ->countAllResults(false),

            // Total students counseled
            'total_students' => $this->db->table('counseling_sessions')
                ->select('DISTINCT student_id')
                ->where('counselor_id', $counselorId)
                ->where('student_id IS NOT NULL')
                ->countAllResults(false),

            // By session type
            'by_type' => $this->sessionModel->getStatistics($counselorId)['by_type'],

            // By status
            'by_status' => $this->sessionModel->getStatistics($counselorId)['by_status'],
        ];

        return $stats;
    }

    /**
     * Get all counseling sessions with filters and pagination
     * 
     * @param int $counselorId
     * @param array $filters
     * @param int $perPage
     * @return array
     */
    public function getAllSessions($counselorId, $filters = [], $perPage = 10)
    {
        $builder = $this->sessionModel
            ->select('counseling_sessions.*,
                      students.nisn, students.nis,
                      users.full_name as student_name,
                      classes.class_name,
                      (SELECT COUNT(*) FROM session_notes WHERE session_notes.session_id = counseling_sessions.id AND session_notes.deleted_at IS NULL) as note_count,
                      (SELECT COUNT(*) FROM session_participants WHERE session_participants.session_id = counseling_sessions.id AND session_participants.is_active = 1 AND session_participants.deleted_at IS NULL) as participant_count')
            ->join('students', 'students.id = counseling_sessions.student_id', 'left')
            ->join('users', 'users.id = students.user_id', 'left')
            ->join('classes', 'classes.id = counseling_sessions.class_id', 'left')
            ->where('counseling_sessions.counselor_id', $counselorId);

        // Apply filters
        if (!empty($filters['session_type'])) {
            $builder->where('counseling_sessions.session_type', $filters['session_type']);
        }

        if (!empty($filters['status'])) {
            $builder->where('counseling_sessions.status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('counseling_sessions.session_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('counseling_sessions.session_date <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('counseling_sessions.topic', $filters['search'])
                ->orLike('users.full_name', $filters['search'])
                ->orLike('students.nisn', $filters['search'])
                ->groupEnd();
        }

        // Order by
        $orderBy = $filters['order_by'] ?? 'counseling_sessions.session_date';
        $orderDir = $filters['order_dir'] ?? 'DESC';

        $builder->orderBy($orderBy, $orderDir);

        // Get paginated results
        $sessions = $builder->paginate($perPage);
        $pager = $this->sessionModel->pager;

        return [
            'sessions' => $sessions,
            'pager' => $pager,
            'total' => $pager->getTotal(),
            'per_page' => $perPage,
            'current_page' => $pager->getCurrentPage(),
            'last_page' => $pager->getPageCount(),
        ];
    }

    /**
     * Get session by ID with full details
     * 
     * @param int $sessionId
     * @param int|null $counselorId (for security check)
     * @return array|null
     */
    public function getSessionById($sessionId, $counselorId = null)
    {
        $session = $this->sessionModel->getSessionWithDetails($sessionId);

        if (!$session) {
            return null;
        }

        // Security check: ensure counselor owns this session
        if ($counselorId && $session['counselor_id'] != $counselorId) {
            return null;
        }

        return $session;
    }

    /**
     * Create new counseling session
     * 
     * @param array $data
     * @return array ['success' => bool, 'message' => string, 'session_id' => int|null]
     */
    public function createSession($data)
    {
        try {
            // Validate session type and required fields
            if ($data['session_type'] === 'Individu' && empty($data['student_id'])) {
                return [
                    'success' => false,
                    'message' => 'Sesi individu harus memiliki siswa',
                ];
            }

            if ($data['session_type'] === 'Klasikal' && empty($data['class_id'])) {
                return [
                    'success' => false,
                    'message' => 'Sesi klasikal harus memiliki kelas',
                ];
            }

            // Check for conflicting sessions
            $hasConflict = $this->sessionModel->hasConflictingSession(
                $data['counselor_id'],
                $data['session_date'],
                $data['session_time'] ?? null
            );

            if ($hasConflict) {
                return [
                    'success' => false,
                    'message' => 'Anda sudah memiliki sesi pada tanggal/waktu tersebut',
                ];
            }

            // Start transaction
            $this->db->transStart();

            // Insert session
            if (!$this->sessionModel->insert($data)) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal membuat sesi: ' . implode(', ', $this->sessionModel->errors()),
                ];
            }

            $sessionId = $this->sessionModel->getInsertID();

            // If group session and participants provided, add them
            if (in_array($data['session_type'], ['Kelompok', 'Klasikal']) && !empty($data['participants'])) {
                $this->participantModel->addMultipleParticipants($sessionId, $data['participants']);
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
            $this->logActivity('create', $sessionId, "Sesi '{$data['topic']}' berhasil dibuat");

            return [
                'success' => true,
                'message' => 'Sesi konseling berhasil dibuat',
                'session_id' => $sessionId,
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creating session: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update counseling session
     * 
     * @param int $sessionId
     * @param array $data
     * @param int|null $counselorId (for security check)
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateSession($sessionId, $data, $counselorId = null)
    {
        try {
            // Check if session exists
            $session = $this->sessionModel->find($sessionId);
            if (!$session) {
                return [
                    'success' => false,
                    'message' => 'Sesi tidak ditemukan',
                ];
            }

            // Security check
            if ($counselorId && $session['counselor_id'] != $counselorId) {
                return [
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengubah sesi ini',
                ];
            }

            // Check for conflicting sessions (exclude current session)
            if (isset($data['session_date']) && isset($data['session_time'])) {
                $hasConflict = $this->sessionModel->hasConflictingSession(
                    $session['counselor_id'],
                    $data['session_date'],
                    $data['session_time'],
                    $sessionId
                );

                if ($hasConflict) {
                    return [
                        'success' => false,
                        'message' => 'Anda sudah memiliki sesi pada tanggal/waktu tersebut',
                    ];
                }
            }

            // Start transaction
            $this->db->transStart();

            // Update session
            if (!$this->sessionModel->update($sessionId, $data)) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal mengupdate sesi: ' . implode(', ', $this->sessionModel->errors()),
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
            $this->logActivity('update', $sessionId, "Sesi berhasil diupdate");

            return [
                'success' => true,
                'message' => 'Sesi konseling berhasil diupdate',
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error updating session: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete counseling session
     * 
     * @param int $sessionId
     * @param int|null $counselorId (for security check)
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteSession($sessionId, $counselorId = null)
    {
        try {
            // Check if session exists
            $session = $this->sessionModel->find($sessionId);
            if (!$session) {
                return [
                    'success' => false,
                    'message' => 'Sesi tidak ditemukan',
                ];
            }

            // Security check
            if ($counselorId && $session['counselor_id'] != $counselorId) {
                return [
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus sesi ini',
                ];
            }

            // Start transaction
            $this->db->transStart();

            // Soft delete session (will cascade to notes and participants)
            if (!$this->sessionModel->delete($sessionId)) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus sesi',
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
            $this->logActivity('delete', $sessionId, "Sesi '{$session['topic']}' berhasil dihapus");

            return [
                'success' => true,
                'message' => 'Sesi konseling berhasil dihapus',
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error deleting session: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get upcoming sessions for counselor
     * 
     * @param int $counselorId
     * @param int $limit
     * @return array
     */
    public function getUpcomingSessions($counselorId, $limit = 5)
    {
        return $this->sessionModel->getUpcomingSessions($counselorId, $limit);
    }

    /**
     * Get recent activities (sessions and notes)
     * 
     * @param int $counselorId
     * @param int $limit
     * @return array
     */
    public function getRecentActivities($counselorId, $limit = 10)
    {
        // Get recent sessions
        $recentSessions = $this->sessionModel
            ->select('counseling_sessions.id, counseling_sessions.topic, counseling_sessions.session_date, 
                      counseling_sessions.session_type, counseling_sessions.status, counseling_sessions.created_at,
                      "session" as activity_type')
            ->where('counselor_id', $counselorId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();

        // Get recent notes
        $recentNotes = $this->noteModel
            ->select('session_notes.id, session_notes.note_type, session_notes.created_at,
                      counseling_sessions.topic,
                      "note" as activity_type')
            ->join('counseling_sessions', 'counseling_sessions.id = session_notes.session_id')
            ->where('session_notes.created_by', $counselorId)
            ->orderBy('session_notes.created_at', 'DESC')
            ->limit($limit)
            ->findAll();

        // Merge and sort by date
        $activities = array_merge($recentSessions, $recentNotes);
        usort($activities, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return array_slice($activities, 0, $limit);
    }

    /**
     * Get available students for session
     * 
     * @param int|null $classId
     * @return array
     */
    public function getAvailableStudents($classId = null)
    {
        $builder = $this->studentModel
            ->select('students.id, students.nisn, students.nis, users.full_name, classes.class_name')
            ->join('users', 'users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('students.status', 'Aktif');

        if ($classId) {
            $builder->where('students.class_id', $classId);
        }

        return $builder->orderBy('users.full_name', 'ASC')->findAll();
    }

    /**
     * Log counseling activity
     * 
     * @param string $action
     * @param int $sessionId
     * @param string $description
     * @return void
     */
    private function logActivity($action, $sessionId, $description)
    {
        log_message('info', "[CounselingService] Action: {$action}, Session ID: {$sessionId}, Description: {$description}");
    }
}
