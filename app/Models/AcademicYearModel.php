<?php

/**
 * File Path: app/Models/AcademicYearModel.php
 * 
 * Academic Year Model
 * Mengelola data tahun ajaran dengan validasi untuk memastikan
 * hanya satu tahun ajaran yang aktif dalam satu waktu
 * 
 * @package    SIB-K
 * @subpackage Models
 * @category   Academic Data
 * @author     Development Team
 * @created    2025-01-01
 */

namespace App\Models;

use CodeIgniter\Model;

class AcademicYearModel extends Model
{
    protected $table            = 'academic_years';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'year_name',
        'start_date',
        'end_date',
        'is_active',
        'semester',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'year_name'  => 'required|min_length[7]|max_length[50]|is_unique[academic_years.year_name,id,{id}]',
        'start_date' => 'required|valid_date[Y-m-d]',
        'end_date'   => 'required|valid_date[Y-m-d]',
        'is_active'  => 'permit_empty|in_list[0,1]',
        'semester'   => 'required|in_list[Ganjil,Genap]',
    ];

    protected $validationMessages = [
        'year_name' => [
            'required'   => 'Nama tahun ajaran harus diisi',
            'min_length' => 'Format tahun ajaran: YYYY/YYYY (contoh: 2024/2025)',
            'is_unique'  => 'Tahun ajaran sudah ada',
        ],
        'start_date' => [
            'required'   => 'Tanggal mulai harus diisi',
            'valid_date' => 'Format tanggal tidak valid',
        ],
        'end_date' => [
            'required'   => 'Tanggal selesai harus diisi',
            'valid_date' => 'Format tanggal tidak valid',
        ],
        'semester' => [
            'required' => 'Semester harus dipilih',
            'in_list'  => 'Semester harus Ganjil atau Genap',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['validateDates'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['validateDates', 'ensureSingleActive'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Validate that end_date is after start_date
     */
    protected function validateDates(array $data)
    {
        if (isset($data['data']['start_date']) && isset($data['data']['end_date'])) {
            $startDate = strtotime($data['data']['start_date']);
            $endDate = strtotime($data['data']['end_date']);

            if ($endDate <= $startDate) {
                throw new \RuntimeException('Tanggal selesai harus lebih besar dari tanggal mulai');
            }
        }

        return $data;
    }

    /**
     * Ensure only one academic year is active
     */
    protected function ensureSingleActive(array $data)
    {
        if (isset($data['data']['is_active']) && $data['data']['is_active'] == 1) {
            $id = $data['id'][0] ?? null;

            // Deactivate all other academic years
            $this->where('is_active', 1);

            if ($id) {
                $this->where('id !=', $id);
            }

            $this->set(['is_active' => 0])->update();
        }

        return $data;
    }

    /**
     * Get active academic year
     * 
     * @return array|null
     */
    public function getActiveYear()
    {
        return $this->where('is_active', 1)->first();
    }

    /**
     * Set academic year as active
     * 
     * @param int $yearId
     * @return bool
     */
    public function setActive($yearId)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Deactivate all years
        $this->where('is_active', 1)->set(['is_active' => 0])->update();

        // Activate selected year
        $result = $this->update($yearId, ['is_active' => 1]);

        $db->transComplete();

        return $db->transStatus();
    }

    /**
     * Get academic year with class count
     * 
     * @return array
     */
    public function getAllWithClassCount()
    {
        $db = \Config\Database::connect();

        return $db->table($this->table)
            ->select('academic_years.*, COUNT(classes.id) as class_count')
            ->join('classes', 'classes.academic_year_id = academic_years.id', 'left')
            ->where('academic_years.deleted_at', null)
            ->groupBy('academic_years.id')
            ->orderBy('academic_years.start_date', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get academic year by name
     * 
     * @param string $yearName
     * @return array|null
     */
    public function getByYearName($yearName)
    {
        return $this->where('year_name', $yearName)->first();
    }

    /**
     * Check if academic year can be deleted
     * 
     * @param int $yearId
     * @return bool
     */
    public function canDelete($yearId)
    {
        $db = \Config\Database::connect();

        // Check if year has classes
        $classCount = $db->table('classes')
            ->where('academic_year_id', $yearId)
            ->countAllResults();

        // Check if it's the active year
        $year = $this->find($yearId);
        $isActive = $year && $year['is_active'] == 1;

        return $classCount === 0 && !$isActive;
    }

    /**
     * Get upcoming academic years
     * 
     * @return array
     */
    public function getUpcomingYears()
    {
        return $this->where('start_date >', date('Y-m-d'))
            ->orderBy('start_date', 'ASC')
            ->findAll();
    }

    /**
     * Get past academic years
     * 
     * @return array
     */
    public function getPastYears()
    {
        return $this->where('end_date <', date('Y-m-d'))
            ->orderBy('end_date', 'DESC')
            ->findAll();
    }

    /**
     * Toggle semester for academic year
     * 
     * @param int $yearId
     * @return bool
     */
    public function toggleSemester($yearId)
    {
        $year = $this->find($yearId);

        if (!$year) {
            return false;
        }

        $newSemester = $year['semester'] === 'Ganjil' ? 'Genap' : 'Ganjil';

        return $this->update($yearId, ['semester' => $newSemester]);
    }

    /**
     * Get academic year statistics
     * 
     * @return array
     */
    public function getStatistics()
    {
        $db = \Config\Database::connect();

        $total = $this->where('deleted_at', null)->countAllResults(false);
        $active = $this->where('is_active', 1)->countAllResults(false);
        $upcoming = $this->where('start_date >', date('Y-m-d'))->countAllResults();

        return [
            'total'    => $total,
            'active'   => $active,
            'upcoming' => $upcoming,
            'past'     => $total - $active - $upcoming,
        ];
    }
}
