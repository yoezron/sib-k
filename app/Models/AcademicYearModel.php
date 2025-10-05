<?php

namespace App\Models;

use CodeIgniter\Model;

class AcademicYearModel extends Model
{
    protected $table            = 'academic_years';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'year_name', 'start_date', 'end_date', 'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'year_name' => 'required|max_length[20]|is_unique[academic_years.year_name,id,{id}]',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    public function getActive()
    {
        return $this->where('is_active', 1)->first();
    }

    public function setActive($id)
    {
        // Deactivate all years
        $this->db->table($this->table)->update(['is_active' => 0]);
        
        // Activate the selected year
        return $this->update($id, ['is_active' => 1]);
    }
}
