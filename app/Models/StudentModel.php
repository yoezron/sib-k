<?php

/**
 * File Path: app/Models/StudentModel.php
 * 
 * Student Model
 * Mengelola data siswa dengan relasi ke users, classes, dan parents
 * 
 * @package    SIB-K
 * @subpackage Models
 * @category   Academic Data
 * @author     Development Team
 * @created    2025-01-01
 */

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'user_id',
        'class_id',
        'nisn',
        'nis',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'address',
        'parent_id',
        'admission_date',
        'status',
        'total_violation_points',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'user_id'        => 'required|integer|is_not_unique[users.id]|is_unique[students.user_id,id,{id}]',
        'nisn'           => 'required|min_length[10]|max_length[20]|is_unique[students.nisn,id,{id}]|numeric',
        'nis'            => 'required|min_length[5]|max_length[20]|is_unique[students.nis,id,{id}]',
        'gender'         => 'required|in_list[L,P]',
        'birth_place'    => 'permit_empty|max_length[100]',
        'birth_date'     => 'permit_empty|valid_date[Y-m-d]',
        'religion'       => 'permit_empty|max_length[50]',
        'address'        => 'permit_empty',
        'parent_id'      => 'permit_empty|integer|is_not_unique[users.id]',
        'admission_date' => 'permit_empty|valid_date[Y-m-d]',
        'status'         => 'permit_empty|in_list[Aktif,Alumni,Pindah,Keluar]',
    ];

    protected $validationMessages = [
        'user_id' => [
            'required'  => 'User ID harus diisi',
            'is_unique' => 'User sudah terdaftar sebagai siswa',
        ],
        'nisn' => [
            'required'   => 'NISN harus diisi',
            'min_length' => 'NISN minimal 10 digit',
            'is_unique'  => 'NISN sudah terdaftar',
            'numeric'    => 'NISN harus berupa angka',
        ],
        'nis' => [
            'required'  => 'NIS harus diisi',
            'is_unique' => 'NIS sudah terdaftar',
        ],
        'gender' => [
            'required' => 'Jenis kelamin harus dipilih',
            'in_list'  => 'Jenis kelamin harus L atau P',
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
     * Get student with complete details
     * 
     * @param int $studentId
     * @return array|null
     */
    public function getStudentWithDetails($studentId)
    {
        return $this->select('students.*, 
                             users.username, users.email, users.full_name, users.phone, users.profile_photo,
                             classes.class_name, classes.grade_level, classes.major,
                             academic_years.year_name,
                             parent.full_name as parent_name, parent.phone as parent_phone, parent.email as parent_email')
            ->join('users', 'users.id = students.user_id', 'left')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('academic_years', 'academic_years.id = classes.academic_year_id', 'left')
            ->join('users as parent', 'parent.id = students.parent_id', 'left')
            ->where('students.id', $studentId)
            ->first();
    }

    /**
     * Get student by user ID
     * 
     * @param int $userId
     * @return array|null
     */
    public function getByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }

    /**
     * Get student by NISN
     * 
     * @param string $nisn
     * @return array|null
     */
    public function getByNISN($nisn)
    {
        return $this->where('nisn', $nisn)->first();
    }

    /**
     * Get student by NIS
     * 
     * @param string $nis
     * @return array|null
     */
    public function getByNIS($nis)
    {
        return $this->where('nis', $nis)->first();
    }

    /**
     * Get all students with details
     * 
     * @return array
     */
    public function getAllWithDetails()
    {
        return $this->select('students.*, 
                             users.full_name, users.email, users.phone,
                             classes.class_name, classes.grade_level,
                             academic_years.year_name')
            ->join('users', 'users.id = students.user_id', 'left')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->join('academic_years', 'academic_years.id = classes.academic_year_id', 'left')
            ->where('students.deleted_at', null)
            ->orderBy('users.full_name', 'ASC')
            ->findAll();
    }

    /**
     * Get students by class
     * 
     * @param int $classId
     * @return array
     */
    public function getByClass($classId)
    {
        return $this->select('students.*, users.full_name, users.email, users.phone, users.profile_photo')
            ->join('users', 'users.id = students.user_id', 'left')
            ->where('students.class_id', $classId)
            ->where('students.status', 'Aktif')
            ->orderBy('users.full_name', 'ASC')
            ->findAll();
    }

    /**
     * Get students by grade level
     * 
     * @param string $gradeLevel
     * @return array
     */
    public function getByGradeLevel($gradeLevel)
    {
        return $this->select('students.*, users.full_name, classes.class_name')
            ->join('users', 'users.id = students.user_id', 'left')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('classes.grade_level', $gradeLevel)
            ->where('students.status', 'Aktif')
            ->orderBy('classes.class_name', 'ASC')
            ->orderBy('users.full_name', 'ASC')
            ->findAll();
    }

    /**
     * Get students by parent
     * 
     * @param int $parentId
     * @return array
     */
    public function getByParent($parentId)
    {
        return $this->select('students.*, 
                             users.full_name, users.email,
                             classes.class_name, classes.grade_level')
            ->join('users', 'users.id = students.user_id', 'left')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('students.parent_id', $parentId)
            ->where('students.status', 'Aktif')
            ->findAll();
    }

    /**
     * Search students
     * 
     * @param string $keyword
     * @return array
     */
    public function searchStudents($keyword)
    {
        return $this->select('students.*, 
                             users.full_name, users.email,
                             classes.class_name')
            ->join('users', 'users.id = students.user_id', 'left')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->groupStart()
            ->like('users.full_name', $keyword)
            ->orLike('students.nisn', $keyword)
            ->orLike('students.nis', $keyword)
            ->orLike('classes.class_name', $keyword)
            ->groupEnd()
            ->where('students.deleted_at', null)
            ->orderBy('users.full_name', 'ASC')
            ->findAll();
    }

    /**
     * Update violation points
     * 
     * @param int $studentId
     * @param int $points
     * @param bool $isAddition
     * @return bool
     */
    public function updateViolationPoints($studentId, $points, $isAddition = true)
    {
        $student = $this->find($studentId);

        if (!$student) {
            return false;
        }

        $currentPoints = $student['total_violation_points'] ?? 0;
        $newPoints = $isAddition ? ($currentPoints + $points) : ($currentPoints - $points);
        $newPoints = max(0, $newPoints); // Tidak boleh negatif

        return $this->update($studentId, ['total_violation_points' => $newPoints]);
    }

    /**
     * Change student status
     * 
     * @param int $studentId
     * @param string $status
     * @return bool
     */
    public function changeStatus($studentId, $status)
    {
        return $this->update($studentId, ['status' => $status]);
    }

    /**
     * Assign parent to student
     * 
     * @param int $studentId
     * @param int $parentId
     * @return bool
     */
    public function assignParent($studentId, $parentId)
    {
        return $this->update($studentId, ['parent_id' => $parentId]);
    }

    /**
     * Move student to another class
     * 
     * @param int $studentId
     * @param int $newClassId
     * @return bool
     */
    public function moveToClass($studentId, $newClassId)
    {
        return $this->update($studentId, ['class_id' => $newClassId]);
    }

    /**
     * Get student statistics
     * 
     * @return array
     */
    public function getStatistics()
    {
        $db = \Config\Database::connect();

        $total = $this->where('deleted_at', null)->countAllResults(false);
        $active = $this->where('status', 'Aktif')->countAllResults(false);
        $alumni = $this->where('status', 'Alumni')->countAllResults(false);

        $byGender = $db->table($this->table)
            ->select('gender, COUNT(*) as total')
            ->where('deleted_at', null)
            ->where('status', 'Aktif')
            ->groupBy('gender')
            ->get()
            ->getResultArray();

        $byGrade = $db->table($this->table)
            ->select('classes.grade_level, COUNT(students.id) as total')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('students.deleted_at', null)
            ->where('students.status', 'Aktif')
            ->groupBy('classes.grade_level')
            ->get()
            ->getResultArray();

        return [
            'total'     => $total,
            'active'    => $active,
            'alumni'    => $alumni,
            'by_gender' => $byGender,
            'by_grade'  => $byGrade,
        ];
    }

    /**
     * Get students with high violation points
     * 
     * @param int $threshold
     * @return array
     */
    public function getHighViolationStudents($threshold = 50)
    {
        return $this->select('students.*, 
                             users.full_name,
                             classes.class_name')
            ->join('users', 'users.id = students.user_id', 'left')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->where('students.total_violation_points >=', $threshold)
            ->where('students.status', 'Aktif')
            ->orderBy('students.total_violation_points', 'DESC')
            ->findAll();
    }
}
