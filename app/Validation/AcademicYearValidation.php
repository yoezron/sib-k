<?php

/**
 * File Path: app/Validation/AcademicYearValidation.php
 * 
 * Academic Year Validation
 * Validation rules dan helper methods untuk Academic Year management
 * 
 * @package    SIB-K
 * @subpackage Validation
 * @category   Academic Year Management
 * @author     Development Team
 * @created    2025-01-06
 */

namespace App\Validation;

class AcademicYearValidation
{
    /**
     * Validation rules untuk create academic year
     * 
     * @return array
     */
    public static function createRules()
    {
        return [
            'year_name' => [
                'label' => 'Nama Tahun Ajaran',
                'rules' => 'required|min_length[7]|max_length[50]|regex_match[/^\d{4}\/\d{4}$/]|is_unique[academic_years.year_name]',
                'errors' => [
                    'required' => 'Nama tahun ajaran harus diisi',
                    'min_length' => 'Format tahun ajaran: YYYY/YYYY (contoh: 2024/2025)',
                    'max_length' => 'Nama tahun ajaran maksimal 50 karakter',
                    'regex_match' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2024/2025)',
                    'is_unique' => 'Tahun ajaran sudah ada',
                ]
            ],
            'start_date' => [
                'label' => 'Tanggal Mulai',
                'rules' => 'required|valid_date[Y-m-d]',
                'errors' => [
                    'required' => 'Tanggal mulai harus diisi',
                    'valid_date' => 'Format tanggal tidak valid (YYYY-MM-DD)',
                ]
            ],
            'end_date' => [
                'label' => 'Tanggal Selesai',
                'rules' => 'required|valid_date[Y-m-d]',
                'errors' => [
                    'required' => 'Tanggal selesai harus diisi',
                    'valid_date' => 'Format tanggal tidak valid (YYYY-MM-DD)',
                ]
            ],
            'semester' => [
                'label' => 'Semester',
                'rules' => 'required|in_list[Ganjil,Genap]',
                'errors' => [
                    'required' => 'Semester harus dipilih',
                    'in_list' => 'Semester harus Ganjil atau Genap',
                ]
            ],
            'is_active' => [
                'label' => 'Status',
                'rules' => 'permit_empty|in_list[0,1]',
                'errors' => [
                    'in_list' => 'Status tidak valid',
                ]
            ],
        ];
    }

    /**
     * Validation rules untuk update academic year
     * 
     * @param int $id Academic Year ID yang sedang diedit
     * @return array
     */
    public static function updateRules($id)
    {
        return [
            'year_name' => [
                'label' => 'Nama Tahun Ajaran',
                'rules' => "required|min_length[7]|max_length[50]|regex_match[/^\d{4}\/\d{4}$/]|is_unique[academic_years.year_name,id,{$id}]",
                'errors' => [
                    'required' => 'Nama tahun ajaran harus diisi',
                    'min_length' => 'Format tahun ajaran: YYYY/YYYY (contoh: 2024/2025)',
                    'max_length' => 'Nama tahun ajaran maksimal 50 karakter',
                    'regex_match' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2024/2025)',
                    'is_unique' => 'Tahun ajaran sudah ada',
                ]
            ],
            'start_date' => [
                'label' => 'Tanggal Mulai',
                'rules' => 'required|valid_date[Y-m-d]',
                'errors' => [
                    'required' => 'Tanggal mulai harus diisi',
                    'valid_date' => 'Format tanggal tidak valid (YYYY-MM-DD)',
                ]
            ],
            'end_date' => [
                'label' => 'Tanggal Selesai',
                'rules' => 'required|valid_date[Y-m-d]',
                'errors' => [
                    'required' => 'Tanggal selesai harus diisi',
                    'valid_date' => 'Format tanggal tidak valid (YYYY-MM-DD)',
                ]
            ],
            'semester' => [
                'label' => 'Semester',
                'rules' => 'required|in_list[Ganjil,Genap]',
                'errors' => [
                    'required' => 'Semester harus dipilih',
                    'in_list' => 'Semester harus Ganjil atau Genap',
                ]
            ],
            'is_active' => [
                'label' => 'Status',
                'rules' => 'permit_empty|in_list[0,1]',
                'errors' => [
                    'in_list' => 'Status tidak valid',
                ]
            ],
        ];
    }

    /**
     * Get semester options for dropdown
     * 
     * @return array
     */
    public static function getSemesterOptions()
    {
        return [
            'Ganjil' => 'Semester Ganjil (Juli - Desember)',
            'Genap' => 'Semester Genap (Januari - Juni)',
        ];
    }

    /**
     * Get status options for dropdown
     * 
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            1 => 'Aktif',
            0 => 'Tidak Aktif',
        ];
    }

    /**
     * Validate date range (start_date < end_date)
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validateDateRange($startDate, $endDate)
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);

        if ($start >= $end) {
            return [
                'valid' => false,
                'message' => 'Tanggal selesai harus lebih besar dari tanggal mulai',
            ];
        }

        // Check if duration is reasonable (minimal 3 months, maksimal 1 year)
        $diffDays = ($end - $start) / (60 * 60 * 24);

        if ($diffDays < 90) {
            return [
                'valid' => false,
                'message' => 'Durasi tahun ajaran minimal 3 bulan',
            ];
        }

        if ($diffDays > 400) {
            return [
                'valid' => false,
                'message' => 'Durasi tahun ajaran maksimal 13 bulan',
            ];
        }

        return [
            'valid' => true,
            'message' => 'Date range valid',
        ];
    }

    /**
     * Validate year name format and consistency
     * 
     * @param string $yearName (e.g., "2024/2025")
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validateYearName($yearName)
    {
        // Check format
        if (!preg_match('/^\d{4}\/\d{4}$/', $yearName)) {
            return [
                'valid' => false,
                'message' => 'Format tahun ajaran harus YYYY/YYYY (contoh: 2024/2025)',
            ];
        }

        // Extract years
        list($year1, $year2) = explode('/', $yearName);

        // Check if second year = first year + 1
        if ((int)$year2 !== ((int)$year1 + 1)) {
            return [
                'valid' => false,
                'message' => 'Tahun kedua harus lebih besar 1 dari tahun pertama (contoh: 2024/2025)',
            ];
        }

        // Check if not too far in the future or past
        $currentYear = (int)date('Y');
        if ((int)$year1 < ($currentYear - 5) || (int)$year1 > ($currentYear + 5)) {
            return [
                'valid' => false,
                'message' => 'Tahun ajaran harus dalam rentang 5 tahun dari tahun sekarang',
            ];
        }

        return [
            'valid' => true,
            'message' => 'Year name valid',
        ];
    }

    /**
     * Generate year name from date
     * 
     * @param string $startDate
     * @return string (e.g., "2024/2025")
     */
    public static function generateYearName($startDate)
    {
        $year = date('Y', strtotime($startDate));
        $month = date('m', strtotime($startDate));

        // If start in July or later, it's year/year+1
        // If start in January-June, it's year-1/year
        if ((int)$month >= 7) {
            return $year . '/' . ($year + 1);
        } else {
            return ($year - 1) . '/' . $year;
        }
    }

    /**
     * Suggest semester based on start date
     * 
     * @param string $startDate
     * @return string ('Ganjil' or 'Genap')
     */
    public static function suggestSemester($startDate)
    {
        $month = (int)date('m', strtotime($startDate));

        // July-December = Ganjil
        // January-June = Genap
        return ($month >= 7) ? 'Ganjil' : 'Genap';
    }

    /**
     * Check if academic year can be deleted
     * 
     * @param int $yearId
     * @return array ['can_delete' => bool, 'message' => string, 'class_count' => int]
     */
    public static function canDelete($yearId)
    {
        $classModel = new \App\Models\ClassModel();

        // Check if has classes
        $classCount = $classModel->where('academic_year_id', $yearId)->countAllResults();

        if ($classCount > 0) {
            return [
                'can_delete' => false,
                'message' => "Tidak dapat menghapus tahun ajaran yang memiliki {$classCount} kelas. Hapus kelas terlebih dahulu.",
                'class_count' => $classCount,
            ];
        }

        return [
            'can_delete' => true,
            'message' => 'Tahun ajaran dapat dihapus',
            'class_count' => 0,
        ];
    }

    /**
     * Check if can set as active (deactivate others first)
     * 
     * @param int $yearId
     * @param int|null $excludeYearId (untuk saat update)
     * @return array ['can_activate' => bool, 'current_active' => array|null]
     */
    public static function canSetActive($yearId, $excludeYearId = null)
    {
        $academicYearModel = new \App\Models\AcademicYearModel();

        $query = $academicYearModel->where('is_active', 1);

        if ($excludeYearId) {
            $query->where('id !=', $excludeYearId);
        }

        $currentActive = $query->first();

        return [
            'can_activate' => true, // Always can activate, will deactivate others
            'current_active' => $currentActive,
        ];
    }

    /**
     * Sanitize academic year input data
     * 
     * @param array $data
     * @return array
     */
    public static function sanitizeInput($data)
    {
        $sanitized = [];

        // Trim all string values
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        // Set default value for is_active
        if (!isset($sanitized['is_active'])) {
            $sanitized['is_active'] = 0;
        }

        // Ensure dates are in correct format
        if (isset($sanitized['start_date'])) {
            $sanitized['start_date'] = date('Y-m-d', strtotime($sanitized['start_date']));
        }

        if (isset($sanitized['end_date'])) {
            $sanitized['end_date'] = date('Y-m-d', strtotime($sanitized['end_date']));
        }

        return $sanitized;
    }

    /**
     * Get default date range for new academic year
     * Based on current date and semester
     * 
     * @param string $semester ('Ganjil' or 'Genap')
     * @return array ['start_date' => string, 'end_date' => string]
     */
    public static function getDefaultDateRange($semester = 'Ganjil')
    {
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');

        if ($semester === 'Ganjil') {
            // Ganjil: July - December
            if ($currentMonth >= 7) {
                $startDate = $currentYear . '-07-01';
                $endDate = $currentYear . '-12-31';
            } else {
                $startDate = ($currentYear - 1) . '-07-01';
                $endDate = ($currentYear - 1) . '-12-31';
            }
        } else {
            // Genap: January - June
            if ($currentMonth >= 1 && $currentMonth <= 6) {
                $startDate = $currentYear . '-01-01';
                $endDate = $currentYear . '-06-30';
            } else {
                $startDate = ($currentYear + 1) . '-01-01';
                $endDate = ($currentYear + 1) . '-06-30';
            }
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Parse year name to get year components
     * 
     * @param string $yearName (e.g., "2024/2025")
     * @return array ['year1' => int, 'year2' => int]
     */
    public static function parseYearName($yearName)
    {
        if (!preg_match('/^\d{4}\/\d{4}$/', $yearName)) {
            return ['year1' => 0, 'year2' => 0];
        }

        list($year1, $year2) = explode('/', $yearName);

        return [
            'year1' => (int)$year1,
            'year2' => (int)$year2,
        ];
    }

    /**
     * Check if date is within academic year range
     * 
     * @param string $date
     * @param string $startDate
     * @param string $endDate
     * @return bool
     */
    public static function isDateInRange($date, $startDate, $endDate)
    {
        $dateTimestamp = strtotime($date);
        $startTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate);

        return ($dateTimestamp >= $startTimestamp && $dateTimestamp <= $endTimestamp);
    }

    /**
     * Get academic year duration in days
     * 
     * @param string $startDate
     * @param string $endDate
     * @return int
     */
    public static function getDuration($startDate, $endDate)
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);

        return (int)(($end - $start) / (60 * 60 * 24));
    }
}
