<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassModel extends Model
{
    protected $table            = 'classes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'academic_year_id', 'name', 'grade_level', 'homeroom_teacher_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'academic_year_id' => 'required|integer',
        'name' => 'required|max_length[50]',
        'grade_level' => 'required|in_list[X,XI,XII]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    public function getWithDetails($id)
    {
        return $this->select('classes.*, academic_years.year_name, users.full_name as homeroom_teacher_name')
            ->join('academic_years', 'academic_years.id = classes.academic_year_id')
            ->join('users', 'users.id = classes.homeroom_teacher_id', 'left')
            ->find($id);
    }

    public function getStudentCount($classId)
    {
        return $this->db->table('students')
            ->where('class_id', $classId)
            ->where('status', 'active')
            ->countAllResults();
    }
}
