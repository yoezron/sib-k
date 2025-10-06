<?php

/**
 * File Path: app/Models/AssessmentQuestionModel.php
 * 
 * Assessment Question Model
 * Model untuk mengelola data pertanyaan asesmen
 * 
 * @package    SIB-K
 * @subpackage Models
 * @category   Model
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Models;

use CodeIgniter\Model;

class AssessmentQuestionModel extends Model
{
    protected $table            = 'assessment_questions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'assessment_id',
        'question_text',
        'question_type',
        'options',
        'correct_answer',
        'points',
        'order_number',
        'is_required',
        'explanation',
        'image_url',
        'dimension',
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
        'question_text' => 'required',
        'question_type' => 'required|in_list[Multiple Choice,Essay,True/False,Rating Scale,Checkbox]',
        'points' => 'permit_empty|decimal',
        'order_number' => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'assessment_id' => [
            'required' => 'ID Asesmen harus diisi',
            'integer' => 'ID Asesmen harus berupa angka',
        ],
        'question_text' => [
            'required' => 'Teks pertanyaan harus diisi',
        ],
        'question_type' => [
            'required' => 'Tipe pertanyaan harus dipilih',
            'in_list' => 'Tipe pertanyaan tidak valid',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['encodeOptions'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['encodeOptions'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = ['decodeOptions'];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Encode options to JSON before insert/update
     * 
     * @param array $data
     * @return array
     */
    protected function encodeOptions(array $data)
    {
        if (isset($data['data']['options']) && is_array($data['data']['options'])) {
            $data['data']['options'] = json_encode($data['data']['options']);
        }
        return $data;
    }

    /**
     * Decode options from JSON after find
     * 
     * @param array $data
     * @return array
     */
    protected function decodeOptions(array $data)
    {
        if (isset($data['data'])) {
            // Multiple records
            if (is_array($data['data']) && isset($data['data'][0])) {
                foreach ($data['data'] as $key => $record) {
                    if (isset($record['options']) && is_string($record['options'])) {
                        $data['data'][$key]['options'] = json_decode($record['options'], true);
                    }
                }
            }
            // Single record
            elseif (isset($data['data']['options']) && is_string($data['data']['options'])) {
                $data['data']['options'] = json_decode($data['data']['options'], true);
            }
        }
        return $data;
    }

    /**
     * Get questions by assessment ID
     * 
     * @param int $assessmentId
     * @param bool $ordered
     * @return array
     */
    public function getByAssessment($assessmentId, $ordered = true)
    {
        $builder = $this->where('assessment_id', $assessmentId);

        if ($ordered) {
            $builder->orderBy('order_number', 'ASC');
        }

        return $builder->findAll();
    }

    /**
     * Get question with assessment info
     * 
     * @param int $id
     * @return array|null
     */
    public function getQuestionWithAssessment($id)
    {
        return $this->select('assessment_questions.*, 
                              assessments.title as assessment_title,
                              assessments.assessment_type')
            ->join('assessments', 'assessments.id = assessment_questions.assessment_id')
            ->where('assessment_questions.id', $id)
            ->first();
    }

    /**
     * Get questions by type
     * 
     * @param int $assessmentId
     * @param string $type
     * @return array
     */
    public function getByType($assessmentId, $type)
    {
        return $this->where('assessment_id', $assessmentId)
            ->where('question_type', $type)
            ->orderBy('order_number', 'ASC')
            ->findAll();
    }

    /**
     * Reorder questions
     * 
     * @param array $orderedIds [question_id => order_number]
     * @return bool
     */
    public function reorderQuestions($orderedIds)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($orderedIds as $questionId => $orderNumber) {
                $this->update($questionId, ['order_number' => $orderNumber]);
            }

            $db->transComplete();
            return $db->transStatus();
        } catch (\Exception $e) {
            log_message('error', 'Error reordering questions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Duplicate question
     * 
     * @param int $questionId
     * @param int|null $newAssessmentId
     * @return int|false New question ID or false
     */
    public function duplicateQuestion($questionId, $newAssessmentId = null)
    {
        $question = $this->find($questionId);

        if (!$question) {
            return false;
        }

        // Remove ID and timestamps
        unset($question['id'], $question['created_at'], $question['updated_at'], $question['deleted_at']);

        // Set new assessment ID if provided
        if ($newAssessmentId) {
            $question['assessment_id'] = $newAssessmentId;
        }

        // Get max order number for new position
        $maxOrder = $this->where('assessment_id', $question['assessment_id'])
            ->selectMax('order_number')
            ->first();

        $question['order_number'] = ($maxOrder['order_number'] ?? 0) + 1;

        return $this->insert($question);
    }

    /**
     * Bulk insert questions
     * 
     * @param array $questions
     * @return bool
     */
    public function bulkInsert($questions)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($questions as $question) {
                // Encode options if array
                if (isset($question['options']) && is_array($question['options'])) {
                    $question['options'] = json_encode($question['options']);
                }
                $this->insert($question);
            }

            $db->transComplete();
            return $db->transStatus();
        } catch (\Exception $e) {
            log_message('error', 'Error bulk inserting questions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get question statistics
     * 
     * @param int $questionId
     * @return array
     */
    public function getQuestionStatistics($questionId)
    {
        $db = \Config\Database::connect();

        $stats = [
            'total_answers' => 0,
            'correct_answers' => 0,
            'incorrect_answers' => 0,
            'average_score' => 0,
            'difficulty_level' => 'Medium',
        ];

        // Get answer statistics
        $answers = $db->table('assessment_answers')
            ->select('COUNT(*) as total,
                      SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct,
                      AVG(score) as avg_score')
            ->where('question_id', $questionId)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        if ($answers && $answers['total'] > 0) {
            $stats['total_answers'] = (int) $answers['total'];
            $stats['correct_answers'] = (int) $answers['correct'];
            $stats['incorrect_answers'] = $stats['total_answers'] - $stats['correct_answers'];
            $stats['average_score'] = round((float) $answers['avg_score'], 2);

            // Calculate difficulty level based on correct percentage
            $correctPercentage = ($stats['correct_answers'] / $stats['total_answers']) * 100;

            if ($correctPercentage >= 75) {
                $stats['difficulty_level'] = 'Easy';
            } elseif ($correctPercentage >= 40) {
                $stats['difficulty_level'] = 'Medium';
            } else {
                $stats['difficulty_level'] = 'Hard';
            }
        }

        return $stats;
    }

    /**
     * Get questions count by assessment
     * 
     * @param int $assessmentId
     * @return int
     */
    public function countByAssessment($assessmentId)
    {
        return $this->where('assessment_id', $assessmentId)->countAllResults();
    }

    /**
     * Get questions with answer statistics
     * 
     * @param int $assessmentId
     * @return array
     */
    public function getQuestionsWithStats($assessmentId)
    {
        return $this->select('assessment_questions.*,
                              COUNT(DISTINCT assessment_answers.student_id) as answer_count,
                              AVG(assessment_answers.score) as avg_score,
                              SUM(CASE WHEN assessment_answers.is_correct = 1 THEN 1 ELSE 0 END) as correct_count')
            ->join('assessment_answers', 'assessment_answers.question_id = assessment_questions.id', 'left')
            ->where('assessment_questions.assessment_id', $assessmentId)
            ->groupBy('assessment_questions.id')
            ->orderBy('assessment_questions.order_number', 'ASC')
            ->findAll();
    }

    /**
     * Get question types for dropdown
     * 
     * @return array
     */
    public function getQuestionTypes()
    {
        return [
            'Multiple Choice' => 'Multiple Choice (Pilihan Ganda)',
            'Essay' => 'Essay (Uraian)',
            'True/False' => 'True/False (Benar/Salah)',
            'Rating Scale' => 'Rating Scale (Skala)',
            'Checkbox' => 'Checkbox (Multi-Pilihan)',
        ];
    }

    /**
     * Validate question options based on type
     * 
     * @param string $type
     * @param mixed $options
     * @return bool
     */
    public function validateQuestionOptions($type, $options)
    {
        switch ($type) {
            case 'Multiple Choice':
            case 'Checkbox':
                // Must have array of options
                return is_array($options) && count($options) >= 2;

            case 'True/False':
                // Must have exactly 2 options
                return is_array($options) && count($options) === 2;

            case 'Rating Scale':
                // Must have min and max values
                return is_array($options) &&
                    isset($options['min']) &&
                    isset($options['max']) &&
                    $options['max'] > $options['min'];

            case 'Essay':
                // No options needed
                return true;

            default:
                return false;
        }
    }
}
