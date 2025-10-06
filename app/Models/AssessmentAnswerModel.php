<?php

/**
 * File Path: app/Models/AssessmentAnswerModel.php
 * 
 * Assessment Answer Model
 * Model untuk mengelola data jawaban siswa pada asesmen
 * 
 * @package    SIB-K
 * @subpackage Models
 * @category   Model
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Models;

use CodeIgniter\Model;

class AssessmentAnswerModel extends Model
{
    protected $table            = 'assessment_answers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'question_id',
        'student_id',
        'result_id',
        'answer_text',
        'answer_option',
        'answer_value',
        'is_correct',
        'score',
        'is_auto_graded',
        'graded_by',
        'graded_at',
        'feedback',
        'answered_at',
        'time_spent_seconds',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'question_id' => 'required|integer',
        'student_id' => 'required|integer',
        'result_id' => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'question_id' => [
            'required' => 'ID Pertanyaan harus diisi',
            'integer' => 'ID Pertanyaan harus berupa angka',
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
    protected $beforeInsert   = ['setAnsweredAt'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Set answered_at timestamp before insert
     * 
     * @param array $data
     * @return array
     */
    protected function setAnsweredAt(array $data)
    {
        if (!isset($data['data']['answered_at'])) {
            $data['data']['answered_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Get answers by result ID
     * 
     * @param int $resultId
     * @return array
     */
    public function getByResult($resultId)
    {
        return $this->select('assessment_answers.*,
                              assessment_questions.question_text,
                              assessment_questions.question_type,
                              assessment_questions.options,
                              assessment_questions.points as max_points,
                              assessment_questions.correct_answer')
            ->join('assessment_questions', 'assessment_questions.id = assessment_answers.question_id')
            ->where('assessment_answers.result_id', $resultId)
            ->orderBy('assessment_questions.order_number', 'ASC')
            ->findAll();
    }

    /**
     * Get student answer for specific question
     * 
     * @param int $studentId
     * @param int $questionId
     * @param int|null $resultId
     * @return array|null
     */
    public function getStudentAnswer($studentId, $questionId, $resultId = null)
    {
        $builder = $this->where('student_id', $studentId)
            ->where('question_id', $questionId);

        if ($resultId) {
            $builder->where('result_id', $resultId);
        }

        return $builder->first();
    }

    /**
     * Save or update student answer
     * 
     * @param array $data
     * @return int|bool Answer ID or false
     */
    public function saveAnswer($data)
    {
        // Check if answer already exists
        $existing = $this->getStudentAnswer(
            $data['student_id'],
            $data['question_id'],
            $data['result_id'] ?? null
        );

        if ($existing) {
            // Update existing answer
            $data['updated_at'] = date('Y-m-d H:i:s');
            return $this->update($existing['id'], $data) ? $existing['id'] : false;
        } else {
            // Insert new answer
            return $this->insert($data);
        }
    }

    /**
     * Auto grade answer for objective questions
     * 
     * @param int $answerId
     * @return bool
     */
    public function autoGradeAnswer($answerId)
    {
        $answer = $this->select('assessment_answers.*,
                                assessment_questions.correct_answer,
                                assessment_questions.points,
                                assessment_questions.question_type')
            ->join('assessment_questions', 'assessment_questions.id = assessment_answers.question_id')
            ->find($answerId);

        if (!$answer) {
            return false;
        }

        // Only auto-grade for objective questions
        if (!in_array($answer['question_type'], ['Multiple Choice', 'True/False', 'Checkbox'])) {
            return false;
        }

        $isCorrect = false;
        $score = 0;

        switch ($answer['question_type']) {
            case 'Multiple Choice':
            case 'True/False':
                $isCorrect = ($answer['answer_option'] === $answer['correct_answer']);
                $score = $isCorrect ? $answer['points'] : 0;
                break;

            case 'Checkbox':
                // Compare array of answers
                $studentAnswers = is_string($answer['answer_option'])
                    ? json_decode($answer['answer_option'], true)
                    : $answer['answer_option'];
                $correctAnswers = is_string($answer['correct_answer'])
                    ? json_decode($answer['correct_answer'], true)
                    : $answer['correct_answer'];

                if (is_array($studentAnswers) && is_array($correctAnswers)) {
                    sort($studentAnswers);
                    sort($correctAnswers);
                    $isCorrect = ($studentAnswers === $correctAnswers);
                    $score = $isCorrect ? $answer['points'] : 0;
                }
                break;
        }

        // Update answer with grading result
        return $this->update($answerId, [
            'is_correct' => $isCorrect ? 1 : 0,
            'score' => $score,
            'is_auto_graded' => 1,
            'graded_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Manual grade answer (for essay questions)
     * 
     * @param int $answerId
     * @param float $score
     * @param int $gradedBy
     * @param string|null $feedback
     * @return bool
     */
    public function manualGradeAnswer($answerId, $score, $gradedBy, $feedback = null)
    {
        $answer = $this->select('assessment_answers.*,
                                assessment_questions.points')
            ->join('assessment_questions', 'assessment_questions.id = assessment_answers.question_id')
            ->find($answerId);

        if (!$answer) {
            return false;
        }

        // Validate score doesn't exceed max points
        if ($score > $answer['points']) {
            $score = $answer['points'];
        }

        $updateData = [
            'score' => $score,
            'is_auto_graded' => 0,
            'graded_by' => $gradedBy,
            'graded_at' => date('Y-m-d H:i:s'),
        ];

        if ($feedback) {
            $updateData['feedback'] = $feedback;
        }

        return $this->update($answerId, $updateData);
    }

    /**
     * Bulk auto grade answers for a result
     * 
     * @param int $resultId
     * @return int Number of graded answers
     */
    public function bulkAutoGrade($resultId)
    {
        $answers = $this->select('assessment_answers.id')
            ->join('assessment_questions', 'assessment_questions.id = assessment_answers.question_id')
            ->where('assessment_answers.result_id', $resultId)
            ->whereIn('assessment_questions.question_type', ['Multiple Choice', 'True/False', 'Checkbox'])
            ->where('assessment_answers.is_auto_graded', 0)
            ->findAll();

        $gradedCount = 0;
        foreach ($answers as $answer) {
            if ($this->autoGradeAnswer($answer['id'])) {
                $gradedCount++;
            }
        }

        return $gradedCount;
    }

    /**
     * Get answers needing manual grading
     * 
     * @param int|null $assessmentId
     * @param int|null $counselorId
     * @return array
     */
    public function getNeedingGrading($assessmentId = null, $counselorId = null)
    {
        $builder = $this->select('assessment_answers.*,
                                  assessment_questions.question_text,
                                  assessment_questions.question_type,
                                  assessment_questions.points,
                                  students.full_name as student_name,
                                  students.nisn,
                                  assessments.title as assessment_title')
            ->join('assessment_questions', 'assessment_questions.id = assessment_answers.question_id')
            ->join('assessment_results', 'assessment_results.id = assessment_answers.result_id')
            ->join('students', 'students.id = assessment_answers.student_id')
            ->join('assessments', 'assessments.id = assessment_results.assessment_id')
            ->whereIn('assessment_questions.question_type', ['Essay', 'Rating Scale'])
            ->where('assessment_answers.graded_at', null);

        if ($assessmentId) {
            $builder->where('assessments.id', $assessmentId);
        }

        if ($counselorId) {
            $builder->where('assessments.created_by', $counselorId);
        }

        return $builder->orderBy('assessment_answers.answered_at', 'ASC')
            ->findAll();
    }

    /**
     * Get answer statistics for a question
     * 
     * @param int $questionId
     * @return array
     */
    public function getAnswerStatistics($questionId)
    {
        $db = \Config\Database::connect();

        $stats = [
            'total_answers' => 0,
            'graded' => 0,
            'pending_grading' => 0,
            'correct_count' => 0,
            'incorrect_count' => 0,
            'average_score' => 0,
            'average_time_spent' => 0,
        ];

        $result = $db->table($this->table)
            ->select('COUNT(*) as total,
                      SUM(CASE WHEN graded_at IS NOT NULL THEN 1 ELSE 0 END) as graded,
                      SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct,
                      SUM(CASE WHEN is_correct = 0 THEN 1 ELSE 0 END) as incorrect,
                      AVG(score) as avg_score,
                      AVG(time_spent_seconds) as avg_time')
            ->where('question_id', $questionId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if ($result) {
            $stats['total_answers'] = (int) $result['total'];
            $stats['graded'] = (int) $result['graded'];
            $stats['pending_grading'] = $stats['total_answers'] - $stats['graded'];
            $stats['correct_count'] = (int) $result['correct'];
            $stats['incorrect_count'] = (int) $result['incorrect'];
            $stats['average_score'] = round((float) $result['avg_score'], 2);
            $stats['average_time_spent'] = round((float) $result['avg_time'], 0);
        }

        return $stats;
    }

    /**
     * Get student progress for an assessment
     * 
     * @param int $studentId
     * @param int $assessmentId
     * @return array
     */
    public function getStudentProgress($studentId, $assessmentId)
    {
        $db = \Config\Database::connect();

        // Get total questions
        $totalQuestions = $db->table('assessment_questions')
            ->where('assessment_id', $assessmentId)
            ->where('deleted_at', null)
            ->countAllResults();

        // Get answered questions
        $answeredQuestions = $db->table($this->table)
            ->join('assessment_questions', 'assessment_questions.id = assessment_answers.question_id')
            ->where('assessment_answers.student_id', $studentId)
            ->where('assessment_questions.assessment_id', $assessmentId)
            ->where('assessment_answers.deleted_at', null)
            ->countAllResults();

        $progress = [
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'remaining_questions' => $totalQuestions - $answeredQuestions,
            'progress_percentage' => $totalQuestions > 0
                ? round(($answeredQuestions / $totalQuestions) * 100, 2)
                : 0,
        ];

        return $progress;
    }

    /**
     * Delete answers by result ID
     * 
     * @param int $resultId
     * @return bool
     */
    public function deleteByResult($resultId)
    {
        return $this->where('result_id', $resultId)->delete();
    }

    /**
     * Get answer distribution for Multiple Choice question
     * 
     * @param int $questionId
     * @return array
     */
    public function getAnswerDistribution($questionId)
    {
        $answers = $this->select('answer_option, COUNT(*) as count')
            ->where('question_id', $questionId)
            ->groupBy('answer_option')
            ->findAll();

        $distribution = [];
        foreach ($answers as $answer) {
            $distribution[$answer['answer_option']] = (int) $answer['count'];
        }

        return $distribution;
    }

    /**
     * Calculate time spent on question
     * 
     * @param int $answerId
     * @param int $seconds
     * @return bool
     */
    public function updateTimeSpent($answerId, $seconds)
    {
        return $this->update($answerId, [
            'time_spent_seconds' => $seconds,
        ]);
    }

    /**
     * Get recent answers by student
     * 
     * @param int $studentId
     * @param int $limit
     * @return array
     */
    public function getRecentAnswersByStudent($studentId, $limit = 10)
    {
        return $this->select('assessment_answers.*,
                              assessment_questions.question_text,
                              assessments.title as assessment_title')
            ->join('assessment_questions', 'assessment_questions.id = assessment_answers.question_id')
            ->join('assessment_results', 'assessment_results.id = assessment_answers.result_id')
            ->join('assessments', 'assessments.id = assessment_results.assessment_id')
            ->where('assessment_answers.student_id', $studentId)
            ->orderBy('assessment_answers.answered_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
