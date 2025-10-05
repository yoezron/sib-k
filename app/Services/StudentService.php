<?php

/**
 * File Path: app/Services/StudentService.php
 * 
 * Student Service
 * Business logic layer untuk Student management
 * 
 * @package    SIB-K
 * @subpackage Services
 * @category   Business Logic
 * @author     Development Team
 * @created    2025-01-05
 */

namespace App\Services;

use App\Models\StudentModel;
use App\Models\UserModel;
use App\Models\ClassModel;
use App\Models\AcademicYearModel;
use App\Models\RoleModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class StudentService
{
    protected $studentModel;
    protected $userModel;
    protected $classModel;
    protected $academicYearModel;
    protected $roleModel;
    protected $db;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
        $this->classModel = new ClassModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->roleModel = new RoleModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get all students with filter and pagination
     * 
     * @param array $filters
     * @param int $perPage
     * @return array
     */
    public function getAllStudents($filters = [], $perPage = 10)
    {
        $builder = $this->studentModel
            ->select('students.*, users.full_name, users.email, users.username, users.phone, users.is_active, classes.class_name, classes.grade_level')
            ->join('users', 'users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left');

        // Apply filters
        if (!empty($filters['class_id'])) {
            $builder->where('students.class_id', $filters['class_id']);
        }

        if (!empty($filters['grade_level'])) {
            $builder->where('classes.grade_level', $filters['grade_level']);
        }

        if (!empty($filters['status'])) {
            $builder->where('students.status', $filters['status']);
        }

        if (!empty($filters['gender'])) {
            $builder->where('students.gender', $filters['gender']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('users.full_name', $filters['search'])
                ->orLike('students.nisn', $filters['search'])
                ->orLike('students.nis', $filters['search'])
                ->orLike('users.email', $filters['search'])
                ->groupEnd();
        }

        // Order by
        $orderBy = $filters['order_by'] ?? 'students.created_at';
        $orderDir = $filters['order_dir'] ?? 'DESC';
        $builder->orderBy($orderBy, $orderDir);

        // Paginate
        return [
            'students' => $builder->paginate($perPage),
            'pager' => $this->studentModel->pager,
        ];
    }

    /**
     * Get student by ID with full details
     * 
     * @param int $studentId
     * @return array|null
     */
    public function getStudentById($studentId)
    {
        $student = $this->studentModel
            ->select('students.*, users.full_name, users.email, users.username, users.phone, users.profile_photo, users.is_active, users.last_login, users.created_at as user_created_at, classes.class_name, classes.grade_level')
            ->join('users', 'users.id = students.user_id')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('students.id', $studentId)
            ->first();

        if (!$student) {
            return null;
        }

        // Get parent info if exists
        if ($student['parent_id']) {
            $parent = $this->userModel->find($student['parent_id']);
            $student['parent_name'] = $parent['full_name'] ?? null;
            $student['parent_phone'] = $parent['phone'] ?? null;
        }

        return $student;
    }

    /**
     * Create new student with existing user account
     * 
     * @param array $data
     * @return array ['success' => bool, 'message' => string, 'student_id' => int|null]
     */
    public function createStudent($data)
    {
        $this->db->transStart();

        try {
            // Check if user exists and not already a student
            $user = $this->userModel->find($data['user_id']);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                    'student_id' => null,
                ];
            }

            $existingStudent = $this->studentModel->where('user_id', $data['user_id'])->first();
            if ($existingStudent) {
                return [
                    'success' => false,
                    'message' => 'User sudah terdaftar sebagai siswa',
                    'student_id' => null,
                ];
            }

            // Prepare student data
            $studentData = [
                'user_id' => $data['user_id'],
                'class_id' => $data['class_id'] ?? null,
                'nisn' => $data['nisn'],
                'nis' => $data['nis'],
                'gender' => $data['gender'],
                'birth_place' => $data['birth_place'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'religion' => $data['religion'] ?? null,
                'address' => $data['address'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'admission_date' => $data['admission_date'] ?? date('Y-m-d'),
                'status' => $data['status'] ?? 'Aktif',
                'total_violation_points' => 0,
            ];

            // Insert student
            if (!$this->studentModel->insert($studentData)) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal menyimpan data siswa: ' . implode(', ', $this->studentModel->errors()),
                    'student_id' => null,
                ];
            }

            $studentId = $this->studentModel->getInsertID();

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data',
                    'student_id' => null,
                ];
            }

            // Log activity
            $this->logActivity('create_student', $studentId, "Siswa baru dibuat: {$data['nisn']} - {$user['full_name']}");

            return [
                'success' => true,
                'message' => 'Data siswa berhasil ditambahkan',
                'student_id' => $studentId,
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creating student: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'student_id' => null,
            ];
        }
    }

    /**
     * Create new student with new user account
     * 
     * @param array $data
     * @return array ['success' => bool, 'message' => string, 'student_id' => int|null, 'user_id' => int|null]
     */
    public function createStudentWithUser($data)
    {
        $this->db->transStart();

        try {
            // Get student role ID
            $studentRole = $this->roleModel->where('role_name', 'Siswa')->first();
            if (!$studentRole) {
                return [
                    'success' => false,
                    'message' => 'Role Siswa tidak ditemukan',
                    'student_id' => null,
                    'user_id' => null,
                ];
            }

            // Create user account first
            $userData = [
                'role_id' => $studentRole['id'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'], // Will be hashed by UserModel
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'is_active' => 1,
            ];

            if (!$this->userModel->insert($userData)) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal membuat akun user: ' . implode(', ', $this->userModel->errors()),
                    'student_id' => null,
                    'user_id' => null,
                ];
            }

            $userId = $this->userModel->getInsertID();

            // Create student data
            $studentData = [
                'user_id' => $userId,
                'class_id' => $data['class_id'] ?? null,
                'nisn' => $data['nisn'],
                'nis' => $data['nis'],
                'gender' => $data['gender'],
                'birth_place' => $data['birth_place'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'religion' => $data['religion'] ?? null,
                'address' => $data['address'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'admission_date' => $data['admission_date'] ?? date('Y-m-d'),
                'status' => $data['status'] ?? 'Aktif',
                'total_violation_points' => 0,
            ];

            if (!$this->studentModel->insert($studentData)) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal menyimpan data siswa: ' . implode(', ', $this->studentModel->errors()),
                    'student_id' => null,
                    'user_id' => null,
                ];
            }

            $studentId = $this->studentModel->getInsertID();

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data',
                    'student_id' => null,
                    'user_id' => null,
                ];
            }

            // Log activity
            $this->logActivity('create_student_with_user', $studentId, "Siswa dan user baru dibuat: {$data['nisn']} - {$data['full_name']}");

            return [
                'success' => true,
                'message' => 'Siswa dan akun user berhasil dibuat',
                'student_id' => $studentId,
                'user_id' => $userId,
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creating student with user: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'student_id' => null,
                'user_id' => null,
            ];
        }
    }

    /**
     * Update student data
     * 
     * @param int $studentId
     * @param array $data
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateStudent($studentId, $data)
    {
        $this->db->transStart();

        try {
            // Check if student exists
            $student = $this->studentModel->find($studentId);
            if (!$student) {
                return [
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan',
                ];
            }

            // Prepare update data
            $updateData = [
                'class_id' => $data['class_id'] ?? null,
                'nisn' => $data['nisn'],
                'nis' => $data['nis'],
                'gender' => $data['gender'],
                'birth_place' => $data['birth_place'] ?? null,
                'birth_date' => $data['birth_date'] ?? null,
                'religion' => $data['religion'] ?? null,
                'address' => $data['address'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'admission_date' => $data['admission_date'] ?? null,
                'status' => $data['status'] ?? 'Aktif',
            ];

            // Update student
            if (!$this->studentModel->update($studentId, $updateData)) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal mengupdate data siswa: ' . implode(', ', $this->studentModel->errors()),
                ];
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengupdate data',
                ];
            }

            // Log activity
            $this->logActivity('update_student', $studentId, "Data siswa diupdate: {$data['nisn']}");

            return [
                'success' => true,
                'message' => 'Data siswa berhasil diupdate',
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error updating student: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete student (soft delete)
     * 
     * @param int $studentId
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteStudent($studentId)
    {
        try {
            // Check if student exists
            $student = $this->getStudentById($studentId);
            if (!$student) {
                return [
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan',
                ];
            }

            // Soft delete student
            if (!$this->studentModel->delete($studentId)) {
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus data siswa',
                ];
            }

            // Log activity
            $this->logActivity('delete_student', $studentId, "Siswa dihapus: {$student['nisn']} - {$student['full_name']}");

            return [
                'success' => true,
                'message' => 'Data siswa berhasil dihapus',
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error deleting student: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Change student class
     * 
     * @param int $studentId
     * @param int $newClassId
     * @return array ['success' => bool, 'message' => string]
     */
    public function changeClass($studentId, $newClassId)
    {
        try {
            // Check if student exists
            $student = $this->studentModel->find($studentId);
            if (!$student) {
                return [
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan',
                ];
            }

            // Check if class exists
            $class = $this->classModel->find($newClassId);
            if (!$class) {
                return [
                    'success' => false,
                    'message' => 'Kelas tidak ditemukan',
                ];
            }

            // Update class
            if (!$this->studentModel->update($studentId, ['class_id' => $newClassId])) {
                return [
                    'success' => false,
                    'message' => 'Gagal memindahkan siswa ke kelas baru',
                ];
            }

            // Log activity
            $this->logActivity('change_class', $studentId, "Siswa dipindahkan ke kelas: {$class['class_name']}");

            return [
                'success' => true,
                'message' => "Siswa berhasil dipindahkan ke kelas {$class['class_name']}",
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error changing class: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get student statistics
     * 
     * @return array
     */
    public function getStudentStatistics()
    {
        $stats = [
            'total' => $this->studentModel->countAllResults(false),
            'active' => $this->studentModel->where('status', 'Aktif')->countAllResults(false),
            'alumni' => $this->studentModel->where('status', 'Alumni')->countAllResults(false),
            'moved' => $this->studentModel->where('status', 'Pindah')->countAllResults(false),
            'dropped' => $this->studentModel->where('status', 'Keluar')->countAllResults(false),
            'by_gender' => [],
            'by_grade' => [],
            'by_religion' => [],
        ];

        // Get count by gender
        $genderStats = $this->db->table('students')
            ->select('gender, COUNT(id) as total')
            ->where('deleted_at', null)
            ->groupBy('gender')
            ->get()
            ->getResultArray();

        foreach ($genderStats as $stat) {
            $stats['by_gender'][$stat['gender']] = (int) $stat['total'];
        }

        // Get count by grade
        $gradeStats = $this->db->table('students')
            ->select('classes.grade_level, COUNT(students.id) as total')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('students.deleted_at', null)
            ->where('students.status', 'Aktif')
            ->groupBy('classes.grade_level')
            ->get()
            ->getResultArray();

        foreach ($gradeStats as $stat) {
            $grade = $stat['grade_level'] ?? 'Belum Ada Kelas';
            $stats['by_grade'][$grade] = (int) $stat['total'];
        }

        // Get count by religion
        $religionStats = $this->db->table('students')
            ->select('religion, COUNT(id) as total')
            ->where('deleted_at', null)
            ->where('religion IS NOT NULL')
            ->groupBy('religion')
            ->get()
            ->getResultArray();

        foreach ($religionStats as $stat) {
            $stats['by_religion'][$stat['religion']] = (int) $stat['total'];
        }

        return $stats;
    }

    /**
     * Get available classes for dropdown
     * 
     * @param int|null $academicYearId
     * @return array
     */
    public function getAvailableClasses($academicYearId = null)
    {
        $builder = $this->classModel->select('classes.*, academic_years.year_name')
            ->join('academic_years', 'academic_years.id = classes.academic_year_id')
            ->where('classes.is_active', 1);

        if ($academicYearId) {
            $builder->where('classes.academic_year_id', $academicYearId);
        }

        return $builder->orderBy('classes.grade_level', 'ASC')
            ->orderBy('classes.class_name', 'ASC')
            ->findAll();
    }

    /**
     * Get available parent users for dropdown
     * 
     * @return array
     */
    public function getAvailableParents()
    {
        $parentRole = $this->roleModel->where('role_name', 'Orang Tua')->first();

        if (!$parentRole) {
            return [];
        }

        return $this->userModel
            ->where('role_id', $parentRole['id'])
            ->where('is_active', 1)
            ->orderBy('full_name', 'ASC')
            ->findAll();
    }

    /**
     * Log student activity
     * 
     * @param string $action
     * @param int $studentId
     * @param string $description
     * @return void
     */
    private function logActivity($action, $studentId, $description)
    {
        log_message('info', "[StudentService] Action: {$action}, Student ID: {$studentId}, Description: {$description}");
    }
}
