<?php

/**
 * File Path: app/Services/AssessmentService.php
 * 
 * Assessment Service
 * Business logic layer untuk mengelola asesmen
 * 
 * @package    SIB-K
 * @subpackage Services
 * @category   Service
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Services;

use App\Models\AssessmentModel;
use App\Models\AssessmentQuestionModel;
use App\Models\AssessmentAnswerModel;
use App\Models\AssessmentResultModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class AssessmentService
{
    protected $assessmentModel;
    protected $questionModel;
    protected $answerModel;
    protected $resultModel;
    protected $db;

    public function __construct()
    {
        $this->assessmentModel = new AssessmentModel();
        $this->questionModel = new AssessmentQuestionModel();
        $this->answerModel = new AssessmentAnswerModel();
        $this->resultModel = new AssessmentResultModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Create new assessment with questions
     * 
     * @param array $assessmentData
     * @param array $questions
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function createAssessment($assessmentData, $questions = [])
    {
        $this->db->transStart();

        try {
            // Insert assessment
            $assessmentId = $this->assessmentModel->insert($assessmentData);

            if (!$assessmentId) {
                throw new \Exception('Gagal membuat asesmen: ' . implode(', ', $this->assessmentModel->errors()));
            }

            // Insert questions if provided
            if (!empty($questions)) {
                $totalQuestions = 0;
                foreach ($questions as $index => $question) {
                    $question['assessment_id'] = $assessmentId;
                    $question['order_number'] = $index + 1;

                    if (!$this->questionModel->insert($question)) {
                        throw new \Exception('Gagal menambah pertanyaan: ' . implode(', ', $this->questionModel->errors()));
                    }
                    $totalQuestions++;
                }

                // Update total questions count
                $this->assessmentModel->update($assessmentId, [
                    'total_questions' => $totalQuestions
                ]);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return [
                'success' => true,
                'message' => 'Asesmen berhasil dibuat',
                'data' => [
                    'assessment_id' => $assessmentId
                ]
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creating assessment: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal membuat asesmen: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Update assessment
     * 
     * @param int $assessmentId
     * @param array $assessmentData
     * @return array
     */
    public function updateAssessment($assessmentId, $assessmentData)
    {
        try {
            $result = $this->assessmentModel->update($assessmentId, $assessmentData);

            if (!$result) {
                throw new \Exception('Gagal mengupdate asesmen: ' . implode(', ', $this->assessmentModel->errors()));
            }

            return [
                'success' => true,
                'message' => 'Asesmen berhasil diupdate',
                'data' => ['assessment_id' => $assessmentId]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating assessment: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal mengupdate asesmen: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Add question to assessment
     * 
     * @param int $assessmentId
     * @param array $questionData
     * @return array
     */
    public function addQuestion($assessmentId, $questionData)
    {
        try {
            $questionData['assessment_id'] = $assessmentId;

            // Get max order number
            $maxOrder = $this->questionModel->where('assessment_id', $assessmentId)
                ->selectMax('order_number')
                ->first();

            $questionData['order_number'] = ($maxOrder['order_number'] ?? 0) + 1;

            $questionId = $this->questionModel->insert($questionData);

            if (!$questionId) {
                throw new \Exception('Gagal menambah pertanyaan: ' . implode(', ', $this->questionModel->errors()));
            }

            // Update total questions count
            $totalQuestions = $this->questionModel->countByAssessment($assessmentId);
            $this->assessmentModel->update($assessmentId, [
                'total_questions' => $totalQuestions
            ]);

            return [
                'success' => true,
                'message' => 'Pertanyaan berhasil ditambahkan',
                'data' => ['question_id' => $questionId]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error adding question: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal menambah pertanyaan: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Update question
     * 
     * @param int $questionId
     * @param array $questionData
     * @return array
     */
    public function updateQuestion($questionId, $questionData)
    {
        try {
            $result = $this->questionModel->update($questionId, $questionData);

            if (!$result) {
                throw new \Exception('Gagal mengupdate pertanyaan: ' . implode(', ', $this->questionModel->errors()));
            }

            return [
                'success' => true,
                'message' => 'Pertanyaan berhasil diupdate',
                'data' => ['question_id' => $questionId]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error updating question: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal mengupdate pertanyaan: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Delete question
     * 
     * @param int $questionId
     * @return array
     */
    public function deleteQuestion($questionId)
    {
        try {
            $question = $this->questionModel->find($questionId);

            if (!$question) {
                throw new \Exception('Pertanyaan tidak ditemukan');
            }

            $result = $this->questionModel->delete($questionId);

            if (!$result) {
                throw new \Exception('Gagal menghapus pertanyaan');
            }

            // Update total questions count
            $totalQuestions = $this->questionModel->countByAssessment($question['assessment_id']);
            $this->assessmentModel->update($question['assessment_id'], [
                'total_questions' => $totalQuestions
            ]);

            return [
                'success' => true,
                'message' => 'Pertanyaan berhasil dihapus',
                'data' => null
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error deleting question: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal menghapus pertanyaan: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Assign assessment to students
     * 
     * @param int $assessmentId
     * @param array $studentIds
     * @return array
     */
    public function assignToStudents($assessmentId, $studentIds)
    {
        try {
            $assessment = $this->assessmentModel->find($assessmentId);

            if (!$assessment) {
                throw new \Exception('Asesmen tidak ditemukan');
            }

            // Logic untuk assignment bisa dikembangkan lebih lanjut
            // Misalnya: create notification, send email, dll

            return [
                'success' => true,
                'message' => 'Asesmen berhasil ditugaskan ke ' . count($studentIds) . ' siswa',
                'data' => [
                    'assessment_id' => $assessmentId,
                    'student_count' => count($studentIds)
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error assigning assessment: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal menugaskan asesmen: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Start assessment for student
     * 
     * @param int $assessmentId
     * @param int $studentId
     * @return array
     */
    public function startAssessment($assessmentId, $studentId)
    {
        try {
            // Check if student can take assessment
            $canTake = $this->assessmentModel->canStudentTakeAssessment($assessmentId, $studentId);

            if (!$canTake['can_take']) {
                throw new \Exception($canTake['message']);
            }

            // Start new assessment result
            $resultId = $this->resultModel->startAssessment($assessmentId, $studentId);

            if (!$resultId) {
                throw new \Exception('Gagal memulai asesmen');
            }

            return [
                'success' => true,
                'message' => 'Asesmen dimulai',
                'data' => [
                    'result_id' => $resultId,
                    'attempts_left' => $canTake['attempts_left'] - 1
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error starting assessment: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Submit answer
     * 
     * @param array $answerData
     * @return array
     */
    public function submitAnswer($answerData)
    {
        try {
            $answerId = $this->answerModel->saveAnswer($answerData);

            if (!$answerId) {
                throw new \Exception('Gagal menyimpan jawaban: ' . implode(', ', $this->answerModel->errors()));
            }

            // Auto grade if applicable
            $question = $this->questionModel->find($answerData['question_id']);

            if ($question && in_array($question['question_type'], ['Multiple Choice', 'True/False', 'Checkbox'])) {
                $this->answerModel->autoGradeAnswer($answerId);
            }

            return [
                'success' => true,
                'message' => 'Jawaban berhasil disimpan',
                'data' => ['answer_id' => $answerId]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error submitting answer: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal menyimpan jawaban: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Submit assessment
     * 
     * @param int $resultId
     * @return array
     */
    public function submitAssessment($resultId)
    {
        $this->db->transStart();

        try {
            // Complete the assessment
            if (!$this->resultModel->completeAssessment($resultId)) {
                throw new \Exception('Gagal menyelesaikan asesmen');
            }

            // Auto grade all objective questions
            $this->answerModel->bulkAutoGrade($resultId);

            // Calculate final score
            if (!$this->resultModel->calculateScore($resultId)) {
                throw new \Exception('Gagal menghitung nilai');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            // Get final result
            $result = $this->resultModel->getResultWithDetails($resultId);

            return [
                'success' => true,
                'message' => 'Asesmen berhasil diselesaikan',
                'data' => [
                    'result_id' => $resultId,
                    'score' => $result['percentage'] ?? 0,
                    'is_passed' => $result['is_passed'] ?? 0
                ]
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error submitting assessment: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal menyelesaikan asesmen: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Grade essay answer
     * 
     * @param int $answerId
     * @param float $score
     * @param int $gradedBy
     * @param string|null $feedback
     * @return array
     */
    public function gradeAnswer($answerId, $score, $gradedBy, $feedback = null)
    {
        try {
            $result = $this->answerModel->manualGradeAnswer($answerId, $score, $gradedBy, $feedback);

            if (!$result) {
                throw new \Exception('Gagal menilai jawaban');
            }

            // Get answer to find result_id
            $answer = $this->answerModel->find($answerId);

            if ($answer && $answer['result_id']) {
                // Recalculate result score
                $this->resultModel->calculateScore($answer['result_id']);
            }

            return [
                'success' => true,
                'message' => 'Jawaban berhasil dinilai',
                'data' => ['answer_id' => $answerId]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error grading answer: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal menilai jawaban: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Add interpretation to result
     * 
     * @param int $resultId
     * @param string $interpretation
     * @param string|null $recommendations
     * @param int $reviewedBy
     * @return array
     */
    public function addInterpretation($resultId, $interpretation, $recommendations, $reviewedBy)
    {
        try {
            $result = $this->resultModel->addReview($resultId, $interpretation, $recommendations, $reviewedBy);

            if (!$result) {
                throw new \Exception('Gagal menambah interpretasi');
            }

            return [
                'success' => true,
                'message' => 'Interpretasi berhasil ditambahkan',
                'data' => ['result_id' => $resultId]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error adding interpretation: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal menambah interpretasi: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Publish assessment
     * 
     * @param int $assessmentId
     * @return array
     */
    public function publishAssessment($assessmentId)
    {
        try {
            $assessment = $this->assessmentModel->find($assessmentId);

            if (!$assessment) {
                throw new \Exception('Asesmen tidak ditemukan');
            }

            // Validate assessment has questions
            $questionCount = $this->questionModel->countByAssessment($assessmentId);

            if ($questionCount == 0) {
                throw new \Exception('Tidak dapat mempublikasi asesmen tanpa pertanyaan');
            }

            $result = $this->assessmentModel->update($assessmentId, [
                'is_published' => 1,
                'is_active' => 1
            ]);

            if (!$result) {
                throw new \Exception('Gagal mempublikasi asesmen');
            }

            return [
                'success' => true,
                'message' => 'Asesmen berhasil dipublikasi',
                'data' => ['assessment_id' => $assessmentId]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error publishing assessment: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Unpublish assessment
     * 
     * @param int $assessmentId
     * @return array
     */
    public function unpublishAssessment($assessmentId)
    {
        try {
            $result = $this->assessmentModel->update($assessmentId, [
                'is_published' => 0,
                'is_active' => 0
            ]);

            if (!$result) {
                throw new \Exception('Gagal menonaktifkan publikasi asesmen');
            }

            return [
                'success' => true,
                'message' => 'Publikasi asesmen berhasil dinonaktifkan',
                'data' => ['assessment_id' => $assessmentId]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error unpublishing assessment: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal menonaktifkan publikasi: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Get assessment dashboard data
     * 
     * @param int $counselorId
     * @return array
     */
    public function getDashboardData($counselorId)
    {
        try {
            $stats = $this->assessmentModel->getCounselorStats($counselorId);
            $recentResults = $this->resultModel->getRecentResults(5, ['counselor_id' => $counselorId]);
            $needingGrading = $this->answerModel->getNeedingGrading(null, $counselorId);
            $needingReview = $this->resultModel->getNeedingReview($counselorId);

            return [
                'success' => true,
                'data' => [
                    'statistics' => $stats,
                    'recent_results' => $recentResults,
                    'pending_grading_count' => count($needingGrading),
                    'pending_review_count' => count($needingReview)
                ]
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting dashboard data: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal mengambil data dashboard: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Duplicate assessment
     * 
     * @param int $assessmentId
     * @param int $createdBy
     * @return array
     */
    public function duplicateAssessment($assessmentId, $createdBy)
    {
        $this->db->transStart();

        try {
            $assessment = $this->assessmentModel->find($assessmentId);

            if (!$assessment) {
                throw new \Exception('Asesmen tidak ditemukan');
            }

            // Remove ID and timestamps
            unset($assessment['id'], $assessment['created_at'], $assessment['updated_at'], $assessment['deleted_at']);

            // Modify title
            $assessment['title'] = $assessment['title'] . ' (Salinan)';
            $assessment['created_by'] = $createdBy;
            $assessment['is_published'] = 0;
            $assessment['is_active'] = 0;
            $assessment['total_participants'] = 0;

            // Insert duplicated assessment
            $newAssessmentId = $this->assessmentModel->insert($assessment);

            if (!$newAssessmentId) {
                throw new \Exception('Gagal menduplikasi asesmen');
            }

            // Duplicate questions
            $questions = $this->questionModel->getByAssessment($assessmentId);

            foreach ($questions as $question) {
                $this->questionModel->duplicateQuestion($question['id'], $newAssessmentId);
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return [
                'success' => true,
                'message' => 'Asesmen berhasil diduplikasi',
                'data' => ['assessment_id' => $newAssessmentId]
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error duplicating assessment: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal menduplikasi asesmen: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
