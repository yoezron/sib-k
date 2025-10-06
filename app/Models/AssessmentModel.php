<?php

/**
 * File Path: app/Models/AssessmentModel.php
 * 
 * Assessment Model
 * Model untuk mengelola data asesmen psikologi dan minat bakat
 * 
 * @package    SIB-K
 * @subpackage Models
 * @category   Model
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Models;

use CodeIgniter\Model;

class AssessmentModel extends Model
{
    protected $table            = 'assessments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'description',
        'assessment_type',
        'target_audience',
        'target_class_id',
        'target_grade',
        'created_by',
        'is_active',
        'is_published',
        'start_date',
        'end_date',
        'duration_minutes',
        'passing_score',
        'max_attempts',
        'show_result_immediately',
        'allow_review',
        'instructions',
        'total_questions',
        'total_participants',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'title' => 'required|max_length[200]',
        'assessment_type' => 'required|max_length[50]',
        'target_audience' => 'required|in_list[Individual,Class,Grade,All]',
        'created_by' => 'required|integer',
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Judul asesmen harus diisi',
            'max_length' => 'Judul maksimal 200 karakter',
        ],
        'assessment_type' => [
            'required' => 'Jenis asesmen harus dipilih',
        ],
        'target_audience' => [
            'required' => 'Target peserta harus dipilih',
            'in_list' => 'Target peserta tidak valid',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get all active and published assessments
     * 
     * @return array
     */
    public function getActiveAssessments()
    {
        $today = date('Y-m-d');

        return $this->select('assessments.*,
                              users.full_name as creator_name')
            ->join('users', 'users.id = assessments.created_by')
            ->where('assessments.is_active', 1)
            ->where('assessments.is_published', 1)
            ->groupStart()
            ->where('assessments.start_date <=', $today)
            ->orWhere('assessments.start_date', null)
            ->groupEnd()
            ->groupStart()
            ->where('assessments.end_date >=', $today)
            ->orWhere('assessments.end_date', null)
            ->groupEnd()
            ->orderBy('assessments.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get assessments by counselor
     * 
     * @param int $counselorId
     * @param array $filters
     * @return array
     */
    public function getByCounselor($counselorId, $filters = [])
    {
        $builder = $this->select('assessments.*,
                                  users.full_name as creator_name,
                                  classes.class_name as target_class_name')
            ->join('users', 'users.id = assessments.created_by')
            ->join('classes', 'classes.id = assessments.target_class_id', 'left')
            ->where('assessments.created_by', $counselorId);

        // Apply filters
        if (!empty($filters['assessment_type'])) {
            $builder->where('assessments.assessment_type', $filters['assessment_type']);
        }

        if (!empty($filters['is_published'])) {
            $builder->where('assessments.is_published', $filters['is_published']);
        }

        if (!empty($filters['target_audience'])) {
            $builder->where('assessments.target_audience', $filters['target_audience']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('assessments.title', $filters['search'])
                ->orLike('assessments.description', $filters['search'])
                ->groupEnd();
        }

        $builder->orderBy('assessments.created_at', 'DESC');

        return $builder->findAll();
    }

    /**
     * Get assessment with full details
     * 
     * @param int $id
     * @return array|null
     */
    public function getAssessmentWithDetails($id)
    {
        return $this->select('assessments.*,
                              users.full_name as creator_name,
                              users.email as creator_email,
                              classes.class_name as target_class_name')
            ->join('users', 'users.id = assessments.created_by')
            ->join('classes', 'classes.id = assessments.target_class_id', 'left')
            ->where('assessments.id', $id)
            ->first();
    }

    /**
     * Get available assessments for student
     * 
     * @param int $studentId
     * @return array
     */
    public function getAvailableForStudent($studentId)
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Get student info
        $student = $db->table('students')
            ->select('students.*, classes.grade')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('students.id', $studentId)
            ->get()
            ->getRowArray();

        if (!$student) {
            return [];
        }

        // Build query for available assessments
        $builder = $this->select('assessments.*,
                                  users.full_name as creator_name,
                                  (SELECT COUNT(*) FROM assessment_results 
                                   WHERE assessment_results.assessment_id = assessments.id 
                                   AND assessment_results.student_id = ' . $studentId . '
                                   AND assessment_results.deleted_at IS NULL) as attempt_count')
            ->join('users', 'users.id = assessments.created_by')
            ->where('assessments.is_active', 1)
            ->where('assessments.is_published', 1);

        // Date range filter
        $builder->groupStart()
            ->where('assessments.start_date <=', $today)
            ->orWhere('assessments.start_date', null)
            ->groupEnd();

        $builder->groupStart()
            ->where('assessments.end_date >=', $today)
            ->orWhere('assessments.end_date', null)
            ->groupEnd();

        // Target audience filter
        $builder->groupStart()
            ->where('assessments.target_audience', 'All')
            ->orWhere('assessments.target_audience', 'Individual')
            ->orGroupStart()
            ->where('assessments.target_audience', 'Class')
            ->where('assessments.target_class_id', $student['class_id'])
            ->groupEnd()
            ->orGroupStart()
            ->where('assessments.target_audience', 'Grade')
            ->where('assessments.target_grade', $student['grade'])
            ->groupEnd()
            ->groupEnd();

        $builder->orderBy('assessments.created_at', 'DESC');

        return $builder->findAll();
    }

    /**
     * Publish assessment
     * 
     * @param int $id
     * @return bool
     */
    public function publishAssessment($id)
    {
        return $this->update($id, [
            'is_published' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Unpublish assessment
     * 
     * @param int $id
     * @return bool
     */
    public function unpublishAssessment($id)
    {
        return $this->update($id, [
            'is_published' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Toggle active status
     * 
     * @param int $id
     * @return bool
     */
    public function toggleActiveStatus($id)
    {
        $assessment = $this->find($id);

        if (!$assessment) {
            return false;
        }

        return $this->update($id, [
            'is_active' => $assessment['is_active'] ? 0 : 1,
        ]);
    }

    /**
     * Get assessment statistics
     * 
     * @param int $id
     * @return array
     */
    public function getStatistics($id)
    {
        $db = \Config\Database::connect();

        $stats = [
            'total_participants' => 0,
            'completed' => 0,
            'in_progress' => 0,
            'average_score' => 0,
            'highest_score' => 0,
            'lowest_score' => 0,
            'pass_rate' => 0,
        ];

        // Get result statistics
        $results = $db->table('assessment_results')
            ->select('COUNT(*) as total,
                      SUM(CASE WHEN status = "Completed" OR status = "Graded" THEN 1 ELSE 0 END) as completed,
                      SUM(CASE WHEN status = "In Progress" THEN 1 ELSE 0 END) as in_progress,
                      AVG(CASE WHEN status = "Graded" THEN percentage ELSE NULL END) as avg_score,
                      MAX(percentage) as max_score,
                      MIN(percentage) as min_score,
                      SUM(CASE WHEN is_passed = 1 THEN 1 ELSE 0 END) as passed')
            ->where('assessment_id', $id)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if ($results) {
            $stats['total_participants'] = (int) $results['total'];
            $stats['completed'] = (int) $results['completed'];
            $stats['in_progress'] = (int) $results['in_progress'];
            $stats['average_score'] = round((float) $results['avg_score'], 2);
            $stats['highest_score'] = round((float) $results['max_score'], 2);
            $stats['lowest_score'] = round((float) $results['min_score'], 2);

            if ($results['completed'] > 0) {
                $stats['pass_rate'] = round(($results['passed'] / $results['completed']) * 100, 2);
            }
        }

        return $stats;
    }

    /**
     * Get dashboard statistics for counselor
     * 
     * @param int $counselorId
     * @return array
     */
    public function getCounselorStats($counselorId)
    {
        $stats = [
            'total_assessments' => $this->where('created_by', $counselorId)->countAllResults(false),
            'published' => $this->where('created_by', $counselorId)
                ->where('is_published', 1)
                ->countAllResults(false),
            'draft' => $this->where('created_by', $counselorId)
                ->where('is_published', 0)
                ->countAllResults(false),
            'active' => $this->where('created_by', $counselorId)
                ->where('is_active', 1)
                ->where('is_published', 1)
                ->countAllResults(false),
        ];

        return $stats;
    }

    /**
     * Get assessments by type
     * 
     * @param string $type
     * @return array
     */
    public function getByType($type)
    {
        return $this->where('assessment_type', $type)
            ->where('is_active', 1)
            ->where('is_published', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Check if student can take assessment
     * 
     * @param int $assessmentId
     * @param int $studentId
     * @return array [can_take => bool, message => string, attempts_left => int]
     */
    public function canStudentTakeAssessment($assessmentId, $studentId)
    {
        $assessment = $this->find($assessmentId);

        if (!$assessment) {
            return [
                'can_take' => false,
                'message' => 'Asesmen tidak ditemukan',
                'attempts_left' => 0,
            ];
        }

        // Check if published and active
        if (!$assessment['is_published'] || !$assessment['is_active']) {
            return [
                'can_take' => false,
                'message' => 'Asesmen tidak tersedia',
                'attempts_left' => 0,
            ];
        }

        // Check date range
        $today = date('Y-m-d');
        if ($assessment['start_date'] && $assessment['start_date'] > $today) {
            return [
                'can_take' => false,
                'message' => 'Asesmen belum dimulai',
                'attempts_left' => 0,
            ];
        }

        if ($assessment['end_date'] && $assessment['end_date'] < $today) {
            return [
                'can_take' => false,
                'message' => 'Asesmen sudah berakhir',
                'attempts_left' => 0,
            ];
        }

        // Check max attempts
        $db = \Config\Database::connect();
        $attemptCount = $db->table('assessment_results')
            ->where('assessment_id', $assessmentId)
            ->where('student_id', $studentId)
            ->where('deleted_at', null)
            ->countAllResults();

        $attemptsLeft = $assessment['max_attempts'] - $attemptCount;

        if ($attemptCount >= $assessment['max_attempts']) {
            return [
                'can_take' => false,
                'message' => 'Anda sudah mencapai maksimal percobaan',
                'attempts_left' => 0,
            ];
        }

        // Check if there's an in-progress attempt
        $inProgressCount = $db->table('assessment_results')
            ->where('assessment_id', $assessmentId)
            ->where('student_id', $studentId)
            ->where('status', 'In Progress')
            ->where('deleted_at', null)
            ->countAllResults();

        if ($inProgressCount > 0) {
            return [
                'can_take' => false,
                'message' => 'Anda memiliki percobaan yang belum selesai',
                'attempts_left' => $attemptsLeft,
            ];
        }

        return [
            'can_take' => true,
            'message' => 'Anda dapat mengerjakan asesmen ini',
            'attempts_left' => $attemptsLeft,
        ];
    }

    /**
     * Get assessment types for dropdown
     * 
     * @return array
     */
    public function getAssessmentTypes()
    {
        return [
            'Psikologi' => 'Psikologi',
            'Minat Bakat' => 'Minat Bakat',
            'Kepribadian' => 'Kepribadian',
            'Kecerdasan' => 'Kecerdasan',
            'Career Interest' => 'Career Interest',
            'Learning Style' => 'Learning Style',
            'Motivasi' => 'Motivasi',
            'Lainnya' => 'Lainnya',
        ];
    }
}
