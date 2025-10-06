<?php

/**
 * File Path: app/Models/SessionNoteModel.php
 * 
 * Session Note Model
 * Mengelola catatan-catatan dari setiap sesi konseling
 * 
 * @package    SIB-K
 * @subpackage Models
 * @category   Counseling
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Models;

use CodeIgniter\Model;

class SessionNoteModel extends Model
{
    protected $table            = 'session_notes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'session_id',
        'created_by',
        'note_type',
        'note_content',
        'is_confidential',
        'attachments',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'session_id'      => 'required|integer|is_not_unique[counseling_sessions.id]',
        'created_by'      => 'required|integer|is_not_unique[users.id]',
        'note_type'       => 'required|in_list[Observasi,Diagnosis,Intervensi,Follow-up,Lainnya]',
        'note_content'    => 'required|min_length[10]',
        'is_confidential' => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'session_id' => [
            'required'       => 'Sesi konseling harus dipilih',
            'is_not_unique'  => 'Sesi konseling tidak valid',
        ],
        'created_by' => [
            'required'       => 'Pembuat catatan harus diisi',
            'is_not_unique'  => 'User tidak valid',
        ],
        'note_type' => [
            'required' => 'Jenis catatan harus dipilih',
            'in_list'  => 'Jenis catatan tidak valid',
        ],
        'note_content' => [
            'required'   => 'Isi catatan harus diisi',
            'min_length' => 'Isi catatan minimal 10 karakter',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setDefaultConfidential'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Set default confidential before insert
     * 
     * @param array $data
     * @return array
     */
    protected function setDefaultConfidential(array $data)
    {
        if (!isset($data['data']['is_confidential'])) {
            $data['data']['is_confidential'] = 1;
        }

        return $data;
    }

    /**
     * Get notes by session with author info
     * 
     * @param int $sessionId
     * @param bool $includeConfidential
     * @return array
     */
    public function getNotesBySession($sessionId, $includeConfidential = true)
    {
        $builder = $this->select('session_notes.*,
                                  users.full_name as author_name,
                                  users.email as author_email')
            ->join('users', 'users.id = session_notes.created_by')
            ->where('session_notes.session_id', $sessionId);

        if (!$includeConfidential) {
            $builder->where('session_notes.is_confidential', 0);
        }

        return $builder->orderBy('session_notes.created_at', 'DESC')->findAll();
    }

    /**
     * Get notes by author
     * 
     * @param int $userId
     * @param int|null $limit
     * @return array
     */
    public function getNotesByAuthor($userId, $limit = null)
    {
        $builder = $this->select('session_notes.*,
                                  counseling_sessions.topic,
                                  counseling_sessions.session_date,
                                  students.nisn,
                                  student_users.full_name as student_name')
            ->join('counseling_sessions', 'counseling_sessions.id = session_notes.session_id')
            ->join('students', 'students.id = counseling_sessions.student_id', 'left')
            ->join('users as student_users', 'student_users.id = students.user_id', 'left')
            ->where('session_notes.created_by', $userId)
            ->orderBy('session_notes.created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get recent notes
     * 
     * @param int|null $counselorId
     * @param int $limit
     * @return array
     */
    public function getRecentNotes($counselorId = null, $limit = 10)
    {
        $builder = $this->select('session_notes.*,
                                  users.full_name as author_name,
                                  counseling_sessions.topic,
                                  counseling_sessions.session_date,
                                  students.nisn,
                                  student_users.full_name as student_name')
            ->join('users', 'users.id = session_notes.created_by')
            ->join('counseling_sessions', 'counseling_sessions.id = session_notes.session_id')
            ->join('students', 'students.id = counseling_sessions.student_id', 'left')
            ->join('users as student_users', 'student_users.id = students.user_id', 'left');

        if ($counselorId) {
            $builder->where('session_notes.created_by', $counselorId);
        }

        return $builder->orderBy('session_notes.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Count notes by session
     * 
     * @param int $sessionId
     * @return int
     */
    public function countNotesBySession($sessionId)
    {
        return $this->where('session_id', $sessionId)->countAllResults();
    }

    /**
     * Get notes by type
     * 
     * @param string $noteType
     * @param int|null $sessionId
     * @return array
     */
    public function getNotesByType($noteType, $sessionId = null)
    {
        $builder = $this->select('session_notes.*,
                                  users.full_name as author_name')
            ->join('users', 'users.id = session_notes.created_by')
            ->where('session_notes.note_type', $noteType);

        if ($sessionId) {
            $builder->where('session_notes.session_id', $sessionId);
        }

        return $builder->orderBy('session_notes.created_at', 'DESC')->findAll();
    }

    /**
     * Add attachment to note
     * 
     * @param int $noteId
     * @param string $filePath
     * @return bool
     */
    public function addAttachment($noteId, $filePath)
    {
        $note = $this->find($noteId);
        if (!$note) {
            return false;
        }

        $attachments = $note['attachments'] ? json_decode($note['attachments'], true) : [];
        $attachments[] = $filePath;

        return $this->update($noteId, [
            'attachments' => json_encode($attachments)
        ]);
    }

    /**
     * Remove attachment from note
     * 
     * @param int $noteId
     * @param string $filePath
     * @return bool
     */
    public function removeAttachment($noteId, $filePath)
    {
        $note = $this->find($noteId);
        if (!$note) {
            return false;
        }

        $attachments = $note['attachments'] ? json_decode($note['attachments'], true) : [];
        $attachments = array_filter($attachments, function ($path) use ($filePath) {
            return $path !== $filePath;
        });

        return $this->update($noteId, [
            'attachments' => json_encode(array_values($attachments))
        ]);
    }

    /**
     * Get attachments from note
     * 
     * @param int $noteId
     * @return array
     */
    public function getAttachments($noteId)
    {
        $note = $this->find($noteId);
        if (!$note || !$note['attachments']) {
            return [];
        }

        return json_decode($note['attachments'], true);
    }

    /**
     * Search notes
     * 
     * @param string $keyword
     * @param array $filters
     * @return array
     */
    public function searchNotes($keyword, $filters = [])
    {
        $builder = $this->select('session_notes.*,
                                  users.full_name as author_name,
                                  counseling_sessions.topic,
                                  counseling_sessions.session_date')
            ->join('users', 'users.id = session_notes.created_by')
            ->join('counseling_sessions', 'counseling_sessions.id = session_notes.session_id')
            ->like('session_notes.note_content', $keyword);

        // Apply filters
        if (!empty($filters['note_type'])) {
            $builder->where('session_notes.note_type', $filters['note_type']);
        }

        if (!empty($filters['created_by'])) {
            $builder->where('session_notes.created_by', $filters['created_by']);
        }

        if (!empty($filters['session_id'])) {
            $builder->where('session_notes.session_id', $filters['session_id']);
        }

        if (isset($filters['is_confidential'])) {
            $builder->where('session_notes.is_confidential', $filters['is_confidential']);
        }

        return $builder->orderBy('session_notes.created_at', 'DESC')->findAll();
    }

    /**
     * Get note statistics by type
     * 
     * @param int|null $counselorId
     * @return array
     */
    public function getStatisticsByType($counselorId = null)
    {
        $builder = $this->select('note_type, COUNT(*) as count')
            ->groupBy('note_type');

        if ($counselorId) {
            $builder->where('created_by', $counselorId);
        }

        $results = $builder->get()->getResultArray();

        $stats = [];
        foreach ($results as $result) {
            $stats[$result['note_type']] = (int)$result['count'];
        }

        return $stats;
    }
}
