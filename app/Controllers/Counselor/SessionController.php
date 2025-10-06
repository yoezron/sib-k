<?php

/**
 * File Path: app/Controllers/Counselor/SessionController.php
 * 
 * Session Controller
 * Controller untuk mengelola CRUD counseling sessions
 * 
 * @package    SIB-K
 * @subpackage Controllers/Counselor
 * @category   Counseling
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Controllers\Counselor;

use App\Controllers\BaseController;
use App\Models\CounselingSessionModel;
use App\Models\SessionNoteModel;
use App\Models\SessionParticipantModel;
use App\Models\StudentModel;
use App\Models\ClassModel;
use App\Services\CounselingService;
use App\Validation\SessionValidation;

class SessionController extends BaseController
{
    protected $sessionModel;
    protected $noteModel;
    protected $participantModel;
    protected $studentModel;
    protected $classModel;
    protected $counselingService;
    protected $db;

    public function __construct()
    {
        $this->sessionModel = new CounselingSessionModel();
        $this->noteModel = new SessionNoteModel();
        $this->participantModel = new SessionParticipantModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->counselingService = new CounselingService();
        $this->db = \Config\Database::connect();
    }

    /**
     * Display list of counseling sessions
     * 
     * @return string
     */
    public function index()
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $counselorId = auth_id();

        // Get filter parameters
        $filters = [
            'status' => $this->request->getGet('status'),
            'session_type' => $this->request->getGet('session_type'),
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'student_id' => $this->request->getGet('student_id'),
        ];

        // Get sessions with filters
        $data['sessions'] = $this->counselingService->getSessionsByCounselor($counselorId, $filters);

        // Get filter options
        $data['students'] = $this->getActiveStudents();
        $data['filters'] = $filters;

        // Page metadata
        $data['title'] = 'Daftar Sesi Konseling';
        $data['pageTitle'] = 'Sesi Konseling';
        $data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'url' => base_url('counselor/dashboard')],
            ['title' => 'Sesi Konseling', 'url' => '#', 'active' => true],
        ];

        return view('counselor/sessions/index', $data);
    }

    /**
     * Show create session form
     * 
     * @return string
     */
    public function create()
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Get form options
        $data['students'] = $this->getActiveStudents();
        $data['classes'] = $this->getActiveClasses();

        // Page metadata
        $data['title'] = 'Tambah Sesi Konseling';
        $data['pageTitle'] = 'Tambah Sesi Konseling';
        $data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'url' => base_url('counselor/dashboard')],
            ['title' => 'Sesi Konseling', 'url' => base_url('counselor/sessions')],
            ['title' => 'Tambah', 'url' => '#', 'active' => true],
        ];

        return view('counselor/sessions/create', $data);
    }

    /**
     * Store new counseling session
     * 
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function store()
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Validate input
        $rules = SessionValidation::createRules();

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Get validated data
        $data = $this->request->getPost();

        // Custom validation untuk session type
        $customValidation = SessionValidation::validateSessionType($data);
        if ($customValidation !== true) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $customValidation);
        }

        // Add counselor_id
        $data['counselor_id'] = auth_id();

        // Start transaction
        $this->db->transStart();

        try {
            // Create session
            $sessionId = $this->sessionModel->insert([
                'counselor_id' => $data['counselor_id'],
                'student_id' => $data['student_id'] ?? null,
                'class_id' => $data['class_id'] ?? null,
                'session_type' => $data['session_type'],
                'session_date' => $data['session_date'],
                'session_time' => $data['session_time'] ?? null,
                'location' => $data['location'] ?? null,
                'topic' => $data['topic'],
                'problem_description' => $data['problem_description'] ?? null,
                'is_confidential' => $data['is_confidential'] ?? 1,
                'duration_minutes' => $data['duration_minutes'] ?? null,
                'status' => 'Dijadwalkan',
            ]);

            if (!$sessionId) {
                throw new \Exception('Gagal menyimpan sesi konseling');
            }

            // If session type is Kelompok, add participants
            if ($data['session_type'] === 'Kelompok' && !empty($data['participants'])) {
                foreach ($data['participants'] as $studentId) {
                    $this->participantModel->insert([
                        'session_id' => $sessionId,
                        'student_id' => $studentId,
                        'attendance_status' => 'Hadir',
                        'is_active' => 1,
                    ]);
                }
            }

            // If session type is Klasikal, add all students from class
            if ($data['session_type'] === 'Klasikal' && !empty($data['class_id'])) {
                $classStudents = $this->studentModel
                    ->where('class_id', $data['class_id'])
                    ->where('status', 'Aktif')
                    ->findAll();

                foreach ($classStudents as $student) {
                    $this->participantModel->insert([
                        'session_id' => $sessionId,
                        'student_id' => $student['id'],
                        'attendance_status' => 'Hadir',
                        'is_active' => 1,
                    ]);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            return redirect()->to('counselor/sessions')
                ->with('success', 'Sesi konseling berhasil ditambahkan');
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creating session: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show session detail with notes
     * 
     * @param int $id
     * @return string
     */
    public function show($id)
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Get session with details
        $session = $this->sessionModel->getSessionWithDetails($id);

        if (!$session) {
            return redirect()->to('counselor/sessions')
                ->with('error', 'Sesi konseling tidak ditemukan');
        }

        // Check if counselor owns this session (except koordinator can view all)
        if (!is_koordinator() && $session['counselor_id'] != auth_id()) {
            return redirect()->to('counselor/sessions')
                ->with('error', 'Anda tidak memiliki akses ke sesi ini');
        }

        $data['session'] = $session;

        // Page metadata
        $data['title'] = 'Detail Sesi Konseling';
        $data['pageTitle'] = 'Detail Sesi - ' . $session['topic'];
        $data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'url' => base_url('counselor/dashboard')],
            ['title' => 'Sesi Konseling', 'url' => base_url('counselor/sessions')],
            ['title' => 'Detail', 'url' => '#', 'active' => true],
        ];

        return view('counselor/sessions/detail', $data);
    }

    /**
     * Show edit session form
     * 
     * @param int $id
     * @return string
     */
    public function edit($id)
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Get session
        $session = $this->sessionModel->find($id);

        if (!$session) {
            return redirect()->to('counselor/sessions')
                ->with('error', 'Sesi konseling tidak ditemukan');
        }

        // Check ownership
        if (!is_koordinator() && $session['counselor_id'] != auth_id()) {
            return redirect()->to('counselor/sessions')
                ->with('error', 'Anda tidak memiliki akses ke sesi ini');
        }

        $data['session'] = $session;
        $data['students'] = $this->getActiveStudents();
        $data['classes'] = $this->getActiveClasses();

        // Get participants if group/class session
        if (in_array($session['session_type'], ['Kelompok', 'Klasikal'])) {
            $data['participants'] = $this->participantModel
                ->where('session_id', $id)
                ->findAll();
        }

        // Page metadata
        $data['title'] = 'Edit Sesi Konseling';
        $data['pageTitle'] = 'Edit Sesi - ' . $session['topic'];
        $data['breadcrumbs'] = [
            ['title' => 'Dashboard', 'url' => base_url('counselor/dashboard')],
            ['title' => 'Sesi Konseling', 'url' => base_url('counselor/sessions')],
            ['title' => 'Edit', 'url' => '#', 'active' => true],
        ];

        return view('counselor/sessions/edit', $data);
    }

    /**
     * Update counseling session
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get session
        $session = $this->sessionModel->find($id);

        if (!$session) {
            return redirect()->to('counselor/sessions')
                ->with('error', 'Sesi konseling tidak ditemukan');
        }

        // Check ownership
        if (!is_koordinator() && $session['counselor_id'] != auth_id()) {
            return redirect()->to('counselor/sessions')
                ->with('error', 'Anda tidak memiliki akses ke sesi ini');
        }

        // Validate input
        $rules = SessionValidation::updateRules();

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        // Custom validation for cancellation
        if (isset($data['status'])) {
            $customValidation = SessionValidation::validateCancellation($data);
            if ($customValidation !== true) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $customValidation);
            }
        }

        try {
            // Update session
            $updateData = [
                'session_type' => $data['session_type'],
                'student_id' => $data['student_id'] ?? null,
                'class_id' => $data['class_id'] ?? null,
                'session_date' => $data['session_date'],
                'session_time' => $data['session_time'] ?? null,
                'location' => $data['location'] ?? null,
                'topic' => $data['topic'],
                'problem_description' => $data['problem_description'] ?? null,
                'session_summary' => $data['session_summary'] ?? null,
                'follow_up_plan' => $data['follow_up_plan'] ?? null,
                'status' => $data['status'] ?? $session['status'],
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
                'is_confidential' => $data['is_confidential'] ?? $session['is_confidential'],
                'duration_minutes' => $data['duration_minutes'] ?? null,
            ];

            $this->sessionModel->update($id, $updateData);

            return redirect()->to('counselor/sessions/detail/' . $id)
                ->with('success', 'Sesi konseling berhasil diupdate');
        } catch (\Exception $e) {
            log_message('error', 'Error updating session: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete (soft delete) counseling session
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */


    /**
     * Add note to session (AJAX)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */


    /**
     * Get students by class (AJAX for form)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function getStudentsByClass()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }

        $classId = $this->request->getGet('class_id');

        if (!$classId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Class ID required',
            ]);
        }

        try {
            $students = $this->studentModel
                ->select('students.id, students.nisn, students.nis, users.full_name as student_name')
                ->join('users', 'users.id = students.user_id')
                ->where('students.class_id', $classId)
                ->where('students.status', 'Aktif')
                ->orderBy('users.full_name', 'ASC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $students,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    // ========== HELPER METHODS ==========

    /**
     * Get active students for dropdown
     * 
     * @return array
     */
    private function getActiveStudents()
    {
        return $this->studentModel
            ->select('students.id, students.nisn, students.nis, users.full_name as student_name, classes.class_name')
            ->join('users', 'users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('students.status', 'Aktif')
            ->orderBy('users.full_name', 'ASC')
            ->findAll();
    }

    /**
     * Get active classes for dropdown
     * 
     * @return array
     */
    private function getActiveClasses()
    {
        return $this->classModel
            ->where('is_active', 1)
            ->orderBy('class_name', 'ASC')
            ->findAll();
    }

    /**
     * Delete counseling session
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get session
        $session = $this->sessionModel->find($id);

        if (!$session) {
            return redirect()->to('counselor/sessions')
                ->with('error', 'Sesi konseling tidak ditemukan');
        }

        // Check ownership
        if (!is_koordinator() && $session['counselor_id'] != auth_id()) {
            return redirect()->to('counselor/sessions')
                ->with('error', 'Anda tidak memiliki akses ke sesi ini');
        }

        try {
            // Delete session (will cascade to notes and participants)
            $this->sessionModel->delete($id);

            return redirect()->to('counselor/sessions')
                ->with('success', 'Sesi konseling berhasil dihapus');
        } catch (\Exception $e) {
            log_message('error', 'Error deleting session: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus sesi');
        }
    }

    /**
     * Add note to counseling session
     * 
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function addNote($id)
    {
        // Check authentication
        if (!is_logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        if (!is_guru_bk() && !is_koordinator()) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        // Get session
        $session = $this->sessionModel->find($id);

        if (!$session) {
            return redirect()->to('counselor/sessions')
                ->with('error', 'Sesi konseling tidak ditemukan');
        }

        // Check ownership - allow if koordinator or session owner
        if (!is_koordinator() && $session['counselor_id'] != auth_id()) {
            return redirect()->to('counselor/sessions')
                ->with('error', 'Anda tidak memiliki akses ke sesi ini');
        }

        // Validate input
        $rules = [
            'note_content' => [
                'label' => 'Isi Catatan',
                'rules' => 'required|min_length[10]|max_length[1000]',
                'errors' => [
                    'required' => '{field} harus diisi',
                    'min_length' => '{field} minimal 10 karakter',
                    'max_length' => '{field} maksimal 1000 karakter'
                ]
            ],
            'is_important' => [
                'label' => 'Status Penting',
                'rules' => 'permit_empty|in_list[0,1]'
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();

        try {
            // Prepare note data
            $noteData = [
                'session_id' => $id,
                'counselor_id' => auth_id(),
                'note_content' => $data['note_content'],
                'is_important' => isset($data['is_important']) ? 1 : 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Save note
            $this->noteModel->insert($noteData);

            return redirect()->to('counselor/sessions/detail/' . $id)
                ->with('success', 'Catatan berhasil ditambahkan');
        } catch (\Exception $e) {
            log_message('error', 'Error adding note: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan catatan: ' . $e->getMessage());
        }
    }
}
