<?php

/**
 * File Path: app/Models/AssessmentResultModel.php
 * 
 * Assessment Result Model
 * Model untuk mengelola data hasil asesmen siswa
 * 
 * @package    SIB-K
 * @subpackage Models
 * @category   Model
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Models;

use CodeIgniter\Model;

class AssessmentResultModel extends Model
{
    protected $table            = 'assessment_results';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'assessment_id',
        'student_id',
        'attempt_number',
        'status',
        'total_score',
        'max_score',
        'percentage',
        'is_passed',
        'questions_answered',
        'total_questions',
        'correct_answers',
        'started_at',
        'completed_at',
        'graded_at',
        'time_spent_seconds',
        'interpretation',
        'recommendations',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'assessment_id' => 'required|integer',
        'student_id' => 'required|integer',
        'attempt_number' => 'permit_empty|integer',
        'status' => 'permit_empty|in_list[In Progress,Completed,Graded,Expired]',
    ];

    protected $validationMessages = [
        'assessment_id' => [
            'required' => 'ID Asesmen harus diisi',
            'integer' => 'ID Asesmen harus berupa angka',
        ],
        'student_id' => [
            'required' => 'ID Siswa harus diisi',
            'integer' => 'ID Siswa harus berupa angka',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setAttemptNumber', 'setStartedAt'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Set attempt number before insert
     * 
     * @param array $data
     * @return array
     */
    protected function setAttemptNumber(array $data)
    {
        if (!isset($data['data']['attempt_number'])) {
            // Get max attempt number for this student and assessment
            $maxAttempt = $this->where('assessment_id', $data['data']['assessment_id'])
                ->where('student_id', $data['data']['student_id'])
                ->selectMax('attempt_number')
                ->first();

            $data['data']['attempt_number'] = ($maxAttempt['attempt_number'] ?? 0) + 1;
        }
        return $data;
    }

    /**
     * Set started_at timestamp before insert
     * 
     * @param array $data
     * @return array
     */
    protected function setStartedAt(array $data)
    {
        if (!isset($data['data']['started_at'])) {
            $data['data']['started_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Get result with full details
     * 
     * @param int $id
     * @return array|null
     */
    public function getResultWithDetails($id)
    {
        return $this->select('assessment_results.*,
                              assessments.title as assessment_title,
                              assessments.assessment_type,
                              assessments.passing_score,
                              students.full_name as student_name,
                              students.nisn,
                              students.class_id,
                              classes.class_name,
                              reviewers.full_name as reviewer_name')
            ->join('assessments', 'assessments.id = assessment_results.assessment_id')
            ->join('students', 'students.id = assessment_results.student_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('users as reviewers', 'reviewers.id = assessment_results.reviewed_by', 'left')
            ->where('assessment_results.id', $id)
            ->first();
    }

    /**
     * Get results by assessment
     * 
     * @param int $assessmentId
     * @param array $filters
     * @return array
     */
    public function getByAssessment($assessmentId, $filters = [])
    {
        $builder = $this->select('assessment_results.*,
                                  students.full_name as student_name,
                                  students.nisn,
                                  classes.class_name')
            ->join('students', 'students.id = assessment_results.student_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('assessment_results.assessment_id', $assessmentId);

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('assessment_results.status', $filters['status']);
        }

        if (!empty($filters['class_id'])) {
            $builder->where('students.class_id', $filters['class_id']);
        }

        if (!empty($filters['is_passed'])) {
            $builder->where('assessment_results.is_passed', $filters['is_passed']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('students.full_name', $filters['search'])
                ->orLike('students.nisn', $filters['search'])
                ->groupEnd();
        }

        $builder->orderBy('assessment_results.completed_at', 'DESC');

        return $builder->findAll();
    }

    /**
     * Get student results
     * 
     * @param int $studentId
     * @param array $filters
     * @return array
     */
    public function getByStudent($studentId, $filters = [])
    {
        $builder = $this->select('assessment_results.*,
                                  assessments.title as assessment_title,
                                  assessments.assessment_type,
                                  assessments.passing_score')
            ->join('assessments', 'assessments.id = assessment_results.assessment_id')
            ->where('assessment_results.student_id', $studentId);

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('assessment_results.status', $filters['status']);
        }

        if (!empty($filters['assessment_type'])) {
            $builder->where('assessments.assessment_type', $filters['assessment_type']);
        }

        $builder->orderBy('assessment_results.created_at', 'DESC');

        return $builder->findAll();
    }

    /**
     * Start new assessment attempt
     * 
     * @param int $assessmentId
     * @param int $studentId
     * @return int|false Result ID or false
     */
    public function startAssessment($assessmentId, $studentId)
    {
        // Check if there's an in-progress attempt
        $inProgress = $this->where('assessment_id', $assessmentId)
            ->where('student_id', $studentId)
            ->where('status', 'In Progress')
            ->first();

        if ($inProgress) {
            return $inProgress['id']; // Return existing in-progress result
        }

        // Get assessment info
        $db = \Config\Database::connect();
        $assessment = $db->table('assessments')
            ->where('id', $assessmentId)
            ->get()
            ->getRowArray();

        if (!$assessment) {
            return false;
        }

        // Count total questions
        $totalQuestions = $db->table('assessment_questions')
            ->where('assessment_id', $assessmentId)
            ->where('deleted_at', null)
            ->countAllResults();

        // Calculate max score
        $maxScore = $db->table('assessment_questions')
            ->selectSum('points')
            ->where('assessment_id', $assessmentId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        // Create new result
        $data = [
            'assessment_id' => $assessmentId,
            'student_id' => $studentId,
            'status' => 'In Progress',
            'total_questions' => $totalQuestions,
            'questions_answered' => 0,
            'max_score' => $maxScore['points'] ?? 0,
            'total_score' => 0,
        ];

        return $this->insert($data);
    }

    /**
     * Complete assessment
     * 
     * @param int $resultId
     * @return bool
     */
    public function completeAssessment($resultId)
    {
        $result = $this->find($resultId);

        if (!$result || $result['status'] !== 'In Progress') {
            return false;
        }

        $updateData = [
            'status' => 'Completed',
            'completed_at' => date('Y-m-d H:i:s'),
        ];

        // Calculate time spent
        if ($result['started_at']) {
            $start = strtotime($result['started_at']);
            $end = strtotime($updateData['completed_at']);
            $updateData['time_spent_seconds'] = $end - $start;
        }

        return $this->update($resultId, $updateData);
    }

    /**
     * Calculate and update score
     * 
     * @param int $resultId
     * @return bool
     */
    public function calculateScore($resultId)
    {
        $db = \Config\Database::connect();

        // Get all answers for this result
        $answers = $db->table('assessment_answers')
            ->select('SUM(score) as total_score, 
                      COUNT(*) as answered,
                      SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct')
            ->where('result_id', $resultId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        $result = $this->find($resultId);

        if (!$result) {
            return false;
        }

        $totalScore = (float) ($answers['total_score'] ?? 0);
        $maxScore = (float) $result['max_score'];
        $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;

        // Get assessment passing score
        $assessment = $db->table('assessments')
            ->where('id', $result['assessment_id'])
            ->get()
            ->getRowArray();

        $passingScore = (float) ($assessment['passing_score'] ?? 0);
        $isPassed = $percentage >= $passingScore ? 1 : 0;

        // Update result
        $updateData = [
            'total_score' => $totalScore,
            'percentage' => $percentage,
            'questions_answered' => (int) ($answers['answered'] ?? 0),
            'correct_answers' => (int) ($answers['correct'] ?? 0),
            'is_passed' => $isPassed,
            'status' => 'Graded',
            'graded_at' => date('Y-m-d H:i:s'),
        ];

        return $this->update($resultId, $updateData);
    }

    /**
     * Add interpretation and recommendations
     * 
     * @param int $resultId
     * @param string $interpretation
     * @param string|null $recommendations
     * @param int $reviewedBy
     * @return bool
     */
    public function addReview($resultId, $interpretation, $recommendations, $reviewedBy)
    {
        return $this->update($resultId, [
            'interpretation' => $interpretation,
            'recommendations' => $recommendations,
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get assessment statistics
     * 
     * @param int $assessmentId
     * @return array
     */
    public function getAssessmentStatistics($assessmentId)
    {
        $db = \Config\Database::connect();

        $stats = $db->table($this->table)
            ->select('COUNT(*) as total_participants,
                      SUM(CASE WHEN status = "Completed" OR status = "Graded" THEN 1 ELSE 0 END) as completed,
                      SUM(CASE WHEN status = "In Progress" THEN 1 ELSE 0 END) as in_progress,
                      SUM(CASE WHEN is_passed = 1 THEN 1 ELSE 0 END) as passed,
                      AVG(CASE WHEN status = "Graded" THEN percentage ELSE NULL END) as avg_percentage,
                      MAX(percentage) as highest_percentage,
                      MIN(CASE WHEN status = "Graded" THEN percentage ELSE NULL END) as lowest_percentage,
                      AVG(time_spent_seconds) as avg_time')
            ->where('assessment_id', $assessmentId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        $result = [
            'total_participants' => (int) ($stats['total_participants'] ?? 0),
            'completed' => (int) ($stats['completed'] ?? 0),
            'in_progress' => (int) ($stats['in_progress'] ?? 0),
            'passed' => (int) ($stats['passed'] ?? 0),
            'failed' => 0,
            'pass_rate' => 0,
            'average_score' => round((float) ($stats['avg_percentage'] ?? 0), 2),
            'highest_score' => round((float) ($stats['highest_percentage'] ?? 0), 2),
            'lowest_score' => round((float) ($stats['lowest_percentage'] ?? 0), 2),
            'average_time_minutes' => round((float) ($stats['avg_time'] ?? 0) / 60, 2),
        ];

        $result['failed'] = $result['completed'] - $result['passed'];

        if ($result['completed'] > 0) {
            $result['pass_rate'] = round(($result['passed'] / $result['completed']) * 100, 2);
        }

        return $result;
    }

    /**
     * Get student statistics
     * 
     * @param int $studentId
     * @return array
     */
    public function getStudentStatistics($studentId)
    {
        $db = \Config\Database::connect();

        $stats = $db->table($this->table)
            ->select('COUNT(*) as total_assessments,
                      SUM(CASE WHEN status = "Completed" OR status = "Graded" THEN 1 ELSE 0 END) as completed,
                      SUM(CASE WHEN status = "In Progress" THEN 1 ELSE 0 END) as in_progress,
                      SUM(CASE WHEN is_passed = 1 THEN 1 ELSE 0 END) as passed,
                      AVG(CASE WHEN status = "Graded" THEN percentage ELSE NULL END) as avg_score')
            ->where('student_id', $studentId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        return [
            'total_assessments' => (int) ($stats['total_assessments'] ?? 0),
            'completed' => (int) ($stats['completed'] ?? 0),
            'in_progress' => (int) ($stats['in_progress'] ?? 0),
            'passed' => (int) ($stats['passed'] ?? 0),
            'average_score' => round((float) ($stats['avg_score'] ?? 0), 2),
        ];
    }

    /**
     * Get results needing review
     * 
     * @param int|null $counselorId
     * @return array
     */
    public function getNeedingReview($counselorId = null)
    {
        $builder = $this->select('assessment_results.*,
                                  assessments.title as assessment_title,
                                  students.full_name as student_name,
                                  students.nisn')
            ->join('assessments', 'assessments.id = assessment_results.assessment_id')
            ->join('students', 'students.id = assessment_results.student_id')
            ->where('assessment_results.status', 'Graded')
            ->where('assessment_results.reviewed_by', null);

        if ($counselorId) {
            $builder->where('assessments.created_by', $counselorId);
        }

        return $builder->orderBy('assessment_results.completed_at', 'ASC')
            ->findAll();
    }

    /**
     * Get recent results
     * 
     * @param int $limit
     * @param array $filters
     * @return array
     */
    public function getRecentResults($limit = 10, $filters = [])
    {
        $builder = $this->select('assessment_results.*,
                                  assessments.title as assessment_title,
                                  students.full_name as student_name,
                                  students.nisn')
            ->join('assessments', 'assessments.id = assessment_results.assessment_id')
            ->join('students', 'students.id = assessment_results.student_id')
            ->whereIn('assessment_results.status', ['Completed', 'Graded']);

        if (!empty($filters['counselor_id'])) {
            $builder->where('assessments.created_by', $filters['counselor_id']);
        }

        return $builder->orderBy('assessment_results.completed_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get top performers for assessment
     * 
     * @param int $assessmentId
     * @param int $limit
     * @return array
     */
    public function getTopPerformers($assessmentId, $limit = 10)
    {
        return $this->select('assessment_results.*,
                              students.full_name as student_name,
                              students.nisn,
                              classes.class_name')
            ->join('students', 'students.id = assessment_results.student_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('assessment_results.assessment_id', $assessmentId)
            ->where('assessment_results.status', 'Graded')
            ->orderBy('assessment_results.percentage', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Check if student has in-progress attempt
     * 
     * @param int $assessmentId
     * @param int $studentId
     * @return array|null
     */
    public function getInProgressAttempt($assessmentId, $studentId)
    {
        return $this->where('assessment_id', $assessmentId)
            ->where('student_id', $studentId)
            ->where('status', 'In Progress')
            ->first();
    }

    /**
     * Get student attempt history
     * 
     * @param int $assessmentId
     * @param int $studentId
     * @return array
     */
    public function getAttemptHistory($assessmentId, $studentId)
    {
        return $this->where('assessment_id', $assessmentId)
            ->where('student_id', $studentId)
            ->orderBy('attempt_number', 'DESC')
            ->findAll();
    }

    /**
     * Expire old in-progress results
     * 
     * @param int $hoursOld
     * @return int Number of expired results
     */
    public function expireOldResults($hoursOld = 24)
    {
        $expireTime = date('Y-m-d H:i:s', strtotime("-{$hoursOld} hours"));

        return $this->where('status', 'In Progress')
            ->where('started_at <', $expireTime)
            ->set(['status' => 'Expired'])
            ->update();
    }
}
