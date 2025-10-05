<?php

/**
 * File Path: app/Validation/StudentValidation.php
 * 
 * Student Validation Rules
 * Custom validation rules untuk Student management
 * 
 * @package    SIB-K
 * @subpackage Validation
 * @category   Validation
 * @author     Development Team
 * @created    2025-01-05
 */

namespace App\Validation;

class StudentValidation
{
    /**
     * Validation rules for creating new student
     * 
     * @return array
     */
    public static function createRules()
    {
        return [
            'user_id' => [
                'label' => 'User Account',
                'rules' => 'required|integer|is_not_unique[users.id]|is_unique[students.user_id]',
                'errors' => [
                    'required' => 'User harus dipilih',
                    'integer' => 'User tidak valid',
                    'is_not_unique' => 'User tidak ditemukan',
                    'is_unique' => 'User sudah terdaftar sebagai siswa'
                ]
            ],
            'class_id' => [
                'label' => 'Kelas',
                'rules' => 'permit_empty|integer|is_not_unique[classes.id]',
                'errors' => [
                    'integer' => 'Kelas tidak valid',
                    'is_not_unique' => 'Kelas tidak ditemukan'
                ]
            ],
            'nisn' => [
                'label' => 'NISN',
                'rules' => 'required|min_length[10]|max_length[20]|numeric|is_unique[students.nisn]',
                'errors' => [
                    'required' => 'NISN harus diisi',
                    'min_length' => 'NISN minimal 10 digit',
                    'max_length' => 'NISN maksimal 20 digit',
                    'numeric' => 'NISN hanya boleh berisi angka',
                    'is_unique' => 'NISN sudah terdaftar'
                ]
            ],
            'nis' => [
                'label' => 'NIS',
                'rules' => 'required|min_length[5]|max_length[20]|is_unique[students.nis]',
                'errors' => [
                    'required' => 'NIS harus diisi',
                    'min_length' => 'NIS minimal 5 karakter',
                    'max_length' => 'NIS maksimal 20 karakter',
                    'is_unique' => 'NIS sudah terdaftar'
                ]
            ],
            'gender' => [
                'label' => 'Jenis Kelamin',
                'rules' => 'required|in_list[L,P]',
                'errors' => [
                    'required' => 'Jenis kelamin harus dipilih',
                    'in_list' => 'Jenis kelamin tidak valid'
                ]
            ],
            'birth_place' => [
                'label' => 'Tempat Lahir',
                'rules' => 'permit_empty|max_length[100]',
                'errors' => [
                    'max_length' => 'Tempat lahir maksimal 100 karakter'
                ]
            ],
            'birth_date' => [
                'label' => 'Tanggal Lahir',
                'rules' => 'permit_empty|valid_date[Y-m-d]',
                'errors' => [
                    'valid_date' => 'Format tanggal tidak valid (YYYY-MM-DD)'
                ]
            ],
            'religion' => [
                'label' => 'Agama',
                'rules' => 'permit_empty|max_length[50]|in_list[Islam,Kristen,Katolik,Hindu,Buddha,Konghucu]',
                'errors' => [
                    'max_length' => 'Agama maksimal 50 karakter',
                    'in_list' => 'Agama harus salah satu dari: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu'
                ]
            ],
            'address' => [
                'label' => 'Alamat',
                'rules' => 'permit_empty',
                'errors' => []
            ],
            'parent_id' => [
                'label' => 'Orang Tua/Wali',
                'rules' => 'permit_empty|integer|is_not_unique[users.id]',
                'errors' => [
                    'integer' => 'Orang tua tidak valid',
                    'is_not_unique' => 'Orang tua tidak ditemukan'
                ]
            ],
            'admission_date' => [
                'label' => 'Tanggal Masuk',
                'rules' => 'permit_empty|valid_date[Y-m-d]',
                'errors' => [
                    'valid_date' => 'Format tanggal tidak valid (YYYY-MM-DD)'
                ]
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'permit_empty|in_list[Aktif,Alumni,Pindah,Keluar]',
                'errors' => [
                    'in_list' => 'Status harus salah satu dari: Aktif, Alumni, Pindah, Keluar'
                ]
            ]
        ];
    }

    /**
     * Validation rules for updating student
     * 
     * @param int $studentId
     * @return array
     */
    public static function updateRules($studentId)
    {
        return [
            'class_id' => [
                'label' => 'Kelas',
                'rules' => 'permit_empty|integer|is_not_unique[classes.id]',
                'errors' => [
                    'integer' => 'Kelas tidak valid',
                    'is_not_unique' => 'Kelas tidak ditemukan'
                ]
            ],
            'nisn' => [
                'label' => 'NISN',
                'rules' => "required|min_length[10]|max_length[20]|numeric|is_unique[students.nisn,id,{$studentId}]",
                'errors' => [
                    'required' => 'NISN harus diisi',
                    'min_length' => 'NISN minimal 10 digit',
                    'max_length' => 'NISN maksimal 20 digit',
                    'numeric' => 'NISN hanya boleh berisi angka',
                    'is_unique' => 'NISN sudah terdaftar'
                ]
            ],
            'nis' => [
                'label' => 'NIS',
                'rules' => "required|min_length[5]|max_length[20]|is_unique[students.nis,id,{$studentId}]",
                'errors' => [
                    'required' => 'NIS harus diisi',
                    'min_length' => 'NIS minimal 5 karakter',
                    'max_length' => 'NIS maksimal 20 karakter',
                    'is_unique' => 'NIS sudah terdaftar'
                ]
            ],
            'gender' => [
                'label' => 'Jenis Kelamin',
                'rules' => 'required|in_list[L,P]',
                'errors' => [
                    'required' => 'Jenis kelamin harus dipilih',
                    'in_list' => 'Jenis kelamin tidak valid'
                ]
            ],
            'birth_place' => [
                'label' => 'Tempat Lahir',
                'rules' => 'permit_empty|max_length[100]',
                'errors' => [
                    'max_length' => 'Tempat lahir maksimal 100 karakter'
                ]
            ],
            'birth_date' => [
                'label' => 'Tanggal Lahir',
                'rules' => 'permit_empty|valid_date[Y-m-d]',
                'errors' => [
                    'valid_date' => 'Format tanggal tidak valid (YYYY-MM-DD)'
                ]
            ],
            'religion' => [
                'label' => 'Agama',
                'rules' => 'permit_empty|max_length[50]|in_list[Islam,Kristen,Katolik,Hindu,Buddha,Konghucu]',
                'errors' => [
                    'max_length' => 'Agama maksimal 50 karakter',
                    'in_list' => 'Agama harus salah satu dari: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu'
                ]
            ],
            'address' => [
                'label' => 'Alamat',
                'rules' => 'permit_empty',
                'errors' => []
            ],
            'parent_id' => [
                'label' => 'Orang Tua/Wali',
                'rules' => 'permit_empty|integer|is_not_unique[users.id]',
                'errors' => [
                    'integer' => 'Orang tua tidak valid',
                    'is_not_unique' => 'Orang tua tidak ditemukan'
                ]
            ],
            'admission_date' => [
                'label' => 'Tanggal Masuk',
                'rules' => 'permit_empty|valid_date[Y-m-d]',
                'errors' => [
                    'valid_date' => 'Format tanggal tidak valid (YYYY-MM-DD)'
                ]
            ],
            'status' => [
                'label' => 'Status',
                'rules' => 'permit_empty|in_list[Aktif,Alumni,Pindah,Keluar]',
                'errors' => [
                    'in_list' => 'Status harus salah satu dari: Aktif, Alumni, Pindah, Keluar'
                ]
            ]
        ];
    }

    /**
     * Validation rules for creating student with new user account
     * 
     * @return array
     */
    public static function createWithUserRules()
    {
        $studentRules = self::createRules();

        // Remove user_id validation
        unset($studentRules['user_id']);

        // Add user account fields
        $userRules = [
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[3]|max_length[100]|alpha_numeric|is_unique[users.username]',
                'errors' => [
                    'required' => 'Username harus diisi',
                    'min_length' => 'Username minimal 3 karakter',
                    'max_length' => 'Username maksimal 100 karakter',
                    'alpha_numeric' => 'Username hanya boleh alfanumerik',
                    'is_unique' => 'Username sudah digunakan'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[255]|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'valid_email' => 'Format email tidak valid',
                    'max_length' => 'Email maksimal 255 karakter',
                    'is_unique' => 'Email sudah terdaftar'
                ]
            ],
            'full_name' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|min_length[3]|max_length[255]',
                'errors' => [
                    'required' => 'Nama lengkap harus diisi',
                    'min_length' => 'Nama lengkap minimal 3 karakter',
                    'max_length' => 'Nama lengkap maksimal 255 karakter'
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[6]|max_length[255]',
                'errors' => [
                    'required' => 'Password harus diisi',
                    'min_length' => 'Password minimal 6 karakter',
                    'max_length' => 'Password maksimal 255 karakter'
                ]
            ],
        ];

        return array_merge($userRules, $studentRules);
    }

    /**
     * Validation rules for bulk import students
     * 
     * @return array
     */
    public static function importRules()
    {
        return [
            'import_file' => [
                'label' => 'File Import',
                'rules' => 'uploaded[import_file]|ext_in[import_file,xlsx,xls,csv]|max_size[import_file,5120]',
                'errors' => [
                    'uploaded' => 'File import harus dipilih',
                    'ext_in' => 'Format file harus XLSX, XLS, atau CSV',
                    'max_size' => 'Ukuran file maksimal 5MB'
                ]
            ],
            'class_id' => [
                'label' => 'Kelas Tujuan',
                'rules' => 'permit_empty|integer|is_not_unique[classes.id]',
                'errors' => [
                    'integer' => 'Kelas tidak valid',
                    'is_not_unique' => 'Kelas tidak ditemukan'
                ]
            ]
        ];
    }

    /**
     * Sanitize student input data
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

        // Set default value for status if not provided
        if (!isset($sanitized['status']) || empty($sanitized['status'])) {
            $sanitized['status'] = 'Aktif';
        }

        // Convert empty strings to null for optional fields
        $optionalFields = ['class_id', 'birth_place', 'birth_date', 'religion', 'address', 'parent_id', 'admission_date'];
        foreach ($optionalFields as $field) {
            if (isset($sanitized[$field]) && $sanitized[$field] === '') {
                $sanitized[$field] = null;
            }
        }

        // Initialize total_violation_points to 0
        if (!isset($sanitized['total_violation_points'])) {
            $sanitized['total_violation_points'] = 0;
        }

        return $sanitized;
    }

    /**
     * Validate student age (must be between 6-25 years)
     * 
     * @param string $birthDate
     * @param string &$error
     * @return bool
     */
    public static function validateAge($birthDate, &$error)
    {
        if (empty($birthDate)) {
            return true; // Allow empty birth date
        }

        try {
            $birth = new \DateTime($birthDate);
            $today = new \DateTime();
            $age = $today->diff($birth)->y;

            if ($age < 6 || $age > 25) {
                $error = 'Usia siswa harus antara 6-25 tahun';
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $error = 'Format tanggal lahir tidak valid';
            return false;
        }
    }

    /**
     * Get available religion options
     * 
     * @return array
     */
    public static function getReligionOptions()
    {
        return [
            'Islam',
            'Kristen',
            'Katolik',
            'Hindu',
            'Buddha',
            'Konghucu'
        ];
    }

    /**
     * Get available status options
     * 
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            'Aktif',
            'Alumni',
            'Pindah',
            'Keluar'
        ];
    }

    /**
     * Get available gender options
     * 
     * @return array
     */
    public static function getGenderOptions()
    {
        return [
            'L' => 'Laki-laki',
            'P' => 'Perempuan'
        ];
    }
}
