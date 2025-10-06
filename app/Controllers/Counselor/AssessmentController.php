<?php

/**
 * File Path: app/Controllers/Counselor/AssessmentController.php
 * 
 * Assessment Controller
 * Controller untuk mengelola asesmen oleh Guru BK
 * 
 * @package    SIB-K
 * @subpackage Controllers
 * @category   Controller
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Controllers\Counselor;

use App\Controllers\BaseController;
use App\Services\AssessmentService;
use App\Models\AssessmentModel;
use App\Models\AssessmentQuestionModel;
use App\Models\AssessmentAnswerModel;
use App\Models\AssessmentResultModel;
use App\Models\StudentModel;
use App\Models\ClassModel;

class AssessmentController extends BaseController
{
    protected $assessmentService;
    protected $assessmentModel;
    protected $questionModel;
    protected $answerModel;
    protected $resultModel;
    protected $studentModel;
    protected $classModel;
    protected $session;

    public function __construct()
    {
        $this->assessmentService = new AssessmentService();
        $this->assessmentModel = new AssessmentModel();
        $this->questionModel = new AssessmentQuestionModel();
        $this->answerModel = new AssessmentAnswerModel();
        $this->resultModel = new AssessmentResultModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->session = session();
    }

    /**
     * Display assessments list
     * 
     * @return string
     */
    public function index()
    {
        $counselorId = $this->session->get('user_id');

        // Get filters from request
        $filters = [
            'assessment_type' => $this->request->getGet('assessment_type'),
            'is_published' => $this->request->getGet('is_published'),
            'target_audience' => $this->request->getGet('target_audience'),
            'search' => $this->request->getGet('search'),
        ];

        // Get assessments
        $assessments = $this->assessmentModel->getByCounselor($counselorId, $filters);

        // Get statistics
        $stats = $this->assessmentModel->getCounselorStats($counselorId);

        $data = [
            'title' => 'Daftar Asesmen',
            'assessments' => $assessments,
            'stats' => $stats,
            'filters' => $filters,
            'assessment_types' => $this->assessmentModel->getAssessmentTypes(),
        ];

        return view('counselor/assessments/index', $data);
    }

    /**
     * Show create assessment form
     * 
     * @return string
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Asesmen Baru',
            'assessment_types' => $this->assessmentModel->getAssessmentTypes(),
            'classes' => $this->classModel->findAll(),
        ];

        return view('counselor/assessments/create', $data);
    }

    /**
     * Store new assessment
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function store()
    {
        // Validation rules
        $rules = [
            'title' => 'required|max_length[200]',
            'assessment_type' => 'required',
            'target_audience' => 'required|in_list[Individual,Class,Grade,All]',
            'description' => 'permit_empty',
            'instructions' => 'permit_empty',
            'duration_minutes' => 'permit_empty|integer',
            'passing_score' => 'permit_empty|decimal',
            'max_attempts' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $counselorId = $this->session->get('user_id');

        $assessmentData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'assessment_type' => $this->request->getPost('assessment_type'),
            'target_audience' => $this->request->getPost('target_audience'),
            'target_class_id' => $this->request->getPost('target_class_id'),
            'target_grade' => $this->request->getPost('target_grade'),
            'instructions' => $this->request->getPost('instructions'),
            'duration_minutes' => $this->request->getPost('duration_minutes'),
            'passing_score' => $this->request->getPost('passing_score'),
            'max_attempts' => $this->request->getPost('max_attempts') ?: 1,
            'show_result_immediately' => $this->request->getPost('show_result_immediately') ? 1 : 0,
            'allow_review' => $this->request->getPost('allow_review') ? 1 : 0,
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'created_by' => $counselorId,
            'is_published' => 0,
            'is_active' => 0,
        ];

        $result = $this->assessmentService->createAssessment($assessmentData);

        if ($result['success']) {
            return redirect()->to('/counselor/assessments/' . $result['data']['assessment_id'] . '/questions')
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }
    }

    /**
     * Show assessment detail
     * 
     * @param int $id
     * @return string
     */
    public function show($id)
    {
        $assessment = $this->assessmentModel->getAssessmentWithDetails($id);

        if (!$assessment) {
            return redirect()->to('/counselor/assessments')
                ->with('error', 'Asesmen tidak ditemukan');
        }

        // Check ownership
        if ($assessment['created_by'] != $this->session->get('user_id')) {
            return redirect()->to('/counselor/assessments')
                ->with('error', 'Anda tidak memiliki akses ke asesmen ini');
        }

        $questions = $this->questionModel->getByAssessment($id, true);
        $statistics = $this->assessmentModel->getStatistics($id);

        $data = [
            'title' => $assessment['title'],
            'assessment' => $assessment,
            'questions' => $questions,
            'statistics' => $statistics,
        ];

        return view('counselor/assessments/show', $data);
    }

    /**
     * Show edit assessment form
     * 
     * @param int $id
     * @return string
     */
    public function edit($id)
    {
        $assessment = $this->assessmentModel->find($id);

        if (!$assessment) {
            return redirect()->to('/counselor/assessments')
                ->with('error', 'Asesmen tidak ditemukan');
        }

        // Check ownership
        if ($assessment['created_by'] != $this->session->get('user_id')) {
            return redirect()->to('/counselor/assessments')
                ->with('error', 'Anda tidak memiliki akses ke asesmen ini');
        }

        $data = [
            'title' => 'Edit Asesmen',
            'assessment' => $assessment,
            'assessment_types' => $this->assessmentModel->getAssessmentTypes(),
            'classes' => $this->classModel->findAll(),
        ];

        return view('counselor/assessments/edit', $data);
    }

    /**
     * Update assessment
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        $assessment = $this->assessmentModel->find($id);

        if (!$assessment || $assessment['created_by'] != $this->session->get('user_id')) {
            return redirect()->to('/counselor/assessments')
                ->with('error', 'Asesmen tidak ditemukan');
        }

        $rules = [
            'title' => 'required|max_length[200]',
            'assessment_type' => 'required',
            'target_audience' => 'required|in_list[Individual,Class,Grade,All]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $assessmentData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'assessment_type' => $this->request->getPost('assessment_type'),
            'target_audience' => $this->request->getPost('target_audience'),
            'target_class_id' => $this->request->getPost('target_class_id'),
            'target_grade' => $this->request->getPost('target_grade'),
            'instructions' => $this->request->getPost('instructions'),
            'duration_minutes' => $this->request->getPost('duration_minutes'),
            'passing_score' => $this->request->getPost('passing_score'),
            'max_attempts' => $this->request->getPost('max_attempts'),
            'show_result_immediately' => $this->request->getPost('show_result_immediately') ? 1 : 0,
            'allow_review' => $this->request->getPost('allow_review') ? 1 : 0,
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
        ];

        $result = $this->assessmentService->updateAssessment($id, $assessmentData);

        if ($result['success']) {
            return redirect()->to('/counselor/assessments/' . $id)
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }
    }

    /**
     * Delete assessment
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        $assessment = $this->assessmentModel->find($id);

        if (!$assessment || $assessment['created_by'] != $this->session->get('user_id')) {
            return redirect()->to('/counselor/assessments')
                ->with('error', 'Asesmen tidak ditemukan');
        }

        if ($this->assessmentModel->delete($id)) {
            return redirect()->to('/counselor/assessments')
                ->with('success', 'Asesmen berhasil dihapus');
        } else {
            return redirect()->to('/counselor/assessments')
                ->with('error', 'Gagal menghapus asesmen');
        }
    }

    /**
     * Manage assessment questions
     * 
     * @param int $id
     * @return string
     */
    public function questions($id)
    {
        $assessment = $this->assessmentModel->find($id);

        if (!$assessment || $assessment['created_by'] != $this->session->get('user_id')) {
            return redirect()->to('/counselor/assessments')
                ->with('error', 'Asesmen tidak ditemukan');
        }

        $questions = $this->questionModel->getByAssessment($id, true);

        $data = [
            'title' => 'Kelola Pertanyaan - ' . $assessment['title'],
            'assessment' => $assessment,
            'questions' => $questions,
            'question_types' => $this->questionModel->getQuestionTypes(),
        ];

        return view('counselor/assessments/questions', $data);
    }

    /**
     * Add question via AJAX
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function addQuestion($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $questionData = [
            'question_text' => $this->request->getPost('question_text'),
            'question_type' => $this->request->getPost('question_type'),
            'options' => $this->request->getPost('options'),
            'correct_answer' => $this->request->getPost('correct_answer'),
            'points' => $this->request->getPost('points') ?: 1,
            'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            'explanation' => $this->request->getPost('explanation'),
            'dimension' => $this->request->getPost('dimension'),
        ];

        $result = $this->assessmentService->addQuestion($id, $questionData);

        return $this->response->setJSON($result);
    }

    /**
     * Update question via AJAX
     * 
     * @param int $id
     * @param int $questionId
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function updateQuestion($id, $questionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $questionData = [
            'question_text' => $this->request->getPost('question_text'),
            'question_type' => $this->request->getPost('question_type'),
            'options' => $this->request->getPost('options'),
            'correct_answer' => $this->request->getPost('correct_answer'),
            'points' => $this->request->getPost('points'),
            'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            'explanation' => $this->request->getPost('explanation'),
            'dimension' => $this->request->getPost('dimension'),
        ];

        $result = $this->assessmentService->updateQuestion($questionId, $questionData);

        return $this->response->setJSON($result);
    }

    /**
     * Delete question via AJAX
     * 
     * @param int $id
     * @param int $questionId
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function deleteQuestion($id, $questionId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $result = $this->assessmentService->deleteQuestion($questionId);

        return $this->response->setJSON($result);
    }

    /**
     * Show assign assessment page
     * 
     * @param int $id
     * @return string
     */
    public function assign($id)
    {
        $assessment = $this->assessmentModel->find($id);

        if (!$assessment || $assessment['created_by'] != $this->session->get('user_id')) {
            return redirect()->to('/counselor/assessments')
                ->with('error', 'Asesmen tidak ditemukan');
        }

        // Get all students grouped by class
        $students = $this->studentModel->select('students.*, classes.class_name')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('students.status', 'Aktif')
            ->orderBy('classes.class_name, students.full_name')
            ->findAll();

        // Group students by class
        $studentsByClass = [];
        foreach ($students as $student) {
            $className = $student['class_name'] ?? 'Tanpa Kelas';
            if (!isset($studentsByClass[$className])) {
                $studentsByClass[$className] = [];
            }
            $studentsByClass[$className][] = $student;
        }

        $data = [
            'title' => 'Tugaskan Asesmen - ' . $assessment['title'],
            'assessment' => $assessment,
            'students_by_class' => $studentsByClass,
            'classes' => $this->classModel->findAll(),
        ];

        return view('counselor/assessments/assign', $data);
    }

    /**
     * Process assign assessment
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function processAssign($id)
    {
        $assessment = $this->assessmentModel->find($id);

        if (!$assessment || $assessment['created_by'] != $this->session->get('user_id')) {
            return redirect()->to('/counselor/assessments')
                ->with('error', 'Asesmen tidak ditemukan');
        }

        $studentIds = $this->request->getPost('student_ids');

        if (empty($studentIds)) {
            return redirect()->back()
                ->with('error', 'Pilih minimal satu siswa');
        }

        $result = $this->assessmentService->assignToStudents($id, $studentIds);

        if ($result['success']) {
            return redirect()->to('/counselor/assessments/' . $id)
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', $result['message']);
        }
    }

    /**
     * Show assessment results
     * 
     * @param int $id
     * @return string
     */
    public function results($id)
    {
        $assessment = $this->assessmentModel->getAssessmentWithDetails($id);

        if (!$assessment || $assessment['created_by'] != $this->session->get('user_id')) {
            return redirect()->to('/counselor/assessments')
                ->with('error', 'Asesmen tidak ditemukan');
        }

        // Get filters
        $filters = [
            'status' => $this->request->getGet('status'),
            'class_id' => $this->request->getGet('class_id'),
            'is_passed' => $this->request->getGet('is_passed'),
            'search' => $this->request->getGet('search'),
        ];

        $results = $this->resultModel->getByAssessment($id, $filters);
        $statistics = $this->resultModel->getAssessmentStatistics($id);

        $data = [
            'title' => 'Hasil Asesmen - ' . $assessment['title'],
            'assessment' => $assessment,
            'results' => $results,
            'statistics' => $statistics,
            'filters' => $filters,
            'classes' => $this->classModel->findAll(),
        ];

        return view('counselor/assessments/results', $data);
    }

    /**
     * Show result detail
     * 
     * @param int $id
     * @param int $resultId
     * @return string
     */
    public function resultDetail($id, $resultId)
    {
        $result = $this->resultModel->getResultWithDetails($resultId);

        if (!$result || $result['assessment_id'] != $id) {
            return redirect()->to('/counselor/assessments/' . $id . '/results')
                ->with('error', 'Hasil tidak ditemukan');
        }

        $answers = $this->answerModel->getByResult($resultId);

        $data = [
            'title' => 'Detail Hasil - ' . $result['student_name'],
            'result' => $result,
            'answers' => $answers,
        ];

        return view('counselor/assessments/result_detail', $data);
    }

    /**
     * Publish assessment
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function publish($id)
    {
        $result = $this->assessmentService->publishAssessment($id);

        if ($result['success']) {
            return redirect()->to('/counselor/assessments/' . $id)
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', $result['message']);
        }
    }

    /**
     * Unpublish assessment
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function unpublish($id)
    {
        $result = $this->assessmentService->unpublishAssessment($id);

        if ($result['success']) {
            return redirect()->to('/counselor/assessments/' . $id)
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', $result['message']);
        }
    }

    /**
     * Duplicate assessment
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function duplicate($id)
    {
        $counselorId = $this->session->get('user_id');
        $result = $this->assessmentService->duplicateAssessment($id, $counselorId);

        if ($result['success']) {
            return redirect()->to('/counselor/assessments/' . $result['data']['assessment_id'])
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', $result['message']);
        }
    }

    /**
     * Grade answers page
     * 
     * @param int $id
     * @return string
     */
    public function grading($id)
    {
        $assessment = $this->assessmentModel->find($id);

        if (!$assessment || $assessment['created_by'] != $this->session->get('user_id')) {
            return redirect()->to('/counselor/assessments')
                ->with('error', 'Asesmen tidak ditemukan');
        }

        $needingGrading = $this->answerModel->getNeedingGrading($id);

        $data = [
            'title' => 'Penilaian Jawaban - ' . $assessment['title'],
            'assessment' => $assessment,
            'answers' => $needingGrading,
        ];

        return view('counselor/assessments/grading', $data);
    }

    /**
     * Submit grade via AJAX
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function submitGrade()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ]);
        }

        $answerId = $this->request->getPost('answer_id');
        $score = $this->request->getPost('score');
        $feedback = $this->request->getPost('feedback');
        $gradedBy = $this->session->get('user_id');

        $result = $this->assessmentService->gradeAnswer($answerId, $score, $gradedBy, $feedback);

        return $this->response->setJSON($result);
    }
}
