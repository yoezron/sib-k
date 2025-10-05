<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 'class_id', 'nisn', 'nis', 'full_name', 'gender',
        'birth_place', 'birth_date', 'address', 'phone', 'email',
        'photo', 'admission_date', 'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'nisn' => 'required|numeric|max_length[20]|is_unique[students.nisn,id,{id}]',
        'full_name' => 'required|max_length[100]',
        'gender' => 'required|in_list[L,P]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    public function getWithClass($id)
    {
        return $this->select('students.*, classes.name as class_name, classes.grade_level')
            ->join('classes', 'classes.id = students.class_id', 'left')
            ->find($id);
    }

    public function getByClass($classId)
    {
        return $this->where('class_id', $classId)
            ->where('status', 'active')
            ->orderBy('full_name', 'ASC')
            ->findAll();
    }
}
