<?php

/**
 * File Path: app/Validation/UserValidation.php
 * 
 * User Validation Rules
 * Custom validation rules untuk User management
 * 
 * @package    SIB-K
 * @subpackage Validation
 * @category   Validation
 * @author     Development Team
 * @created    2025-01-05
 */

namespace App\Validation;

class UserValidation
{
    /**
     * Validation rules for creating new user
     * 
     * @return array
     */
    public static function createRules()
    {
        return [
            'role_id' => [
                'label' => 'Role',
                'rules' => 'required|integer|is_not_unique[roles.id]',
                'errors' => [
                    'required' => 'Role harus dipilih',
                    'integer' => 'Role tidak valid',
                    'is_not_unique' => 'Role tidak ditemukan'
                ]
            ],
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[3]|max_length[100]|alpha_numeric|is_unique[users.username]',
                'errors' => [
                    'required' => 'Username harus diisi',
                    'min_length' => 'Username minimal 3 karakter',
                    'max_length' => 'Username maksimal 100 karakter',
                    'alpha_numeric' => 'Username hanya boleh mengandung huruf dan angka',
                    'is_unique' => 'Username sudah digunakan, silakan pilih yang lain'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[255]|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email harus diisi',
                    'valid_email' => 'Format email tidak valid',
                    'max_length' => 'Email maksimal 255 karakter',
                    'is_unique' => 'Email sudah terdaftar, silakan gunakan email lain'
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
            'password_confirm' => [
                'label' => 'Konfirmasi Password',
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi password harus diisi',
                    'matches' => 'Konfirmasi password tidak sesuai dengan password'
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
            'phone' => [
                'label' => 'Nomor Telepon',
                'rules' => 'permit_empty|min_length[10]|max_length[20]|numeric',
                'errors' => [
                    'min_length' => 'Nomor telepon minimal 10 digit',
                    'max_length' => 'Nomor telepon maksimal 20 digit',
                    'numeric' => 'Nomor telepon hanya boleh berisi angka'
                ]
            ],
            'is_active' => [
                'label' => 'Status Aktif',
                'rules' => 'permit_empty|in_list[0,1]',
                'errors' => [
                    'in_list' => 'Status aktif tidak valid'
                ]
            ]
        ];
    }

    /**
     * Validation rules for updating user
     * 
     * @param int $userId
     * @return array
     */
    public static function updateRules($userId)
    {
        return [
            'role_id' => [
                'label' => 'Role',
                'rules' => 'required|integer|is_not_unique[roles.id]',
                'errors' => [
                    'required' => 'Role harus dipilih',
                    'integer' => 'Role tidak valid',
                    'is_not_unique' => 'Role tidak ditemukan'
                ]
            ],
            'username' => [
                'label' => 'Username',
                'rules' => "required|min_length[3]|max_length[100]|alpha_numeric|is_unique[users.username,id,{$userId}]",
                'errors' => [
                    'required' => 'Username harus diisi',
                    'min_length' => 'Username minimal 3 karakter',
                    'max_length' => 'Username maksimal 100 karakter',
                    'alpha_numeric' => 'Username hanya boleh mengandung huruf dan angka',
                    'is_unique' => 'Username sudah digunakan, silakan pilih yang lain'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => "required|valid_email|max_length[255]|is_unique[users.email,id,{$userId}]",
                'errors' => [
                    'required' => 'Email harus diisi',
                    'valid_email' => 'Format email tidak valid',
                    'max_length' => 'Email maksimal 255 karakter',
                    'is_unique' => 'Email sudah terdaftar, silakan gunakan email lain'
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
            'phone' => [
                'label' => 'Nomor Telepon',
                'rules' => 'permit_empty|min_length[10]|max_length[20]|numeric',
                'errors' => [
                    'min_length' => 'Nomor telepon minimal 10 digit',
                    'max_length' => 'Nomor telepon maksimal 20 digit',
                    'numeric' => 'Nomor telepon hanya boleh berisi angka'
                ]
            ],
            'is_active' => [
                'label' => 'Status Aktif',
                'rules' => 'permit_empty|in_list[0,1]',
                'errors' => [
                    'in_list' => 'Status aktif tidak valid'
                ]
            ]
        ];
    }

    /**
     * Validation rules for changing password
     * 
     * @param bool $requireOldPassword
     * @return array
     */
    public static function changePasswordRules($requireOldPassword = true)
    {
        $rules = [];

        if ($requireOldPassword) {
            $rules['old_password'] = [
                'label' => 'Password Lama',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password lama harus diisi'
                ]
            ];
        }

        $rules['new_password'] = [
            'label' => 'Password Baru',
            'rules' => 'required|min_length[6]|max_length[255]',
            'errors' => [
                'required' => 'Password baru harus diisi',
                'min_length' => 'Password baru minimal 6 karakter',
                'max_length' => 'Password baru maksimal 255 karakter'
            ]
        ];

        $rules['new_password_confirm'] = [
            'label' => 'Konfirmasi Password Baru',
            'rules' => 'required|matches[new_password]',
            'errors' => [
                'required' => 'Konfirmasi password baru harus diisi',
                'matches' => 'Konfirmasi password tidak sesuai dengan password baru'
            ]
        ];

        return $rules;
    }

    /**
     * Validation rules for uploading profile photo
     * 
     * @return array
     */
    public static function profilePhotoRules()
    {
        return [
            'profile_photo' => [
                'label' => 'Foto Profil',
                'rules' => 'uploaded[profile_photo]|is_image[profile_photo]|mime_in[profile_photo,image/jpg,image/jpeg,image/png]|max_size[profile_photo,2048]',
                'errors' => [
                    'uploaded' => 'Foto profil harus dipilih',
                    'is_image' => 'File yang dipilih harus berupa gambar',
                    'mime_in' => 'Format foto harus JPG, JPEG, atau PNG',
                    'max_size' => 'Ukuran foto maksimal 2MB'
                ]
            ]
        ];
    }

    /**
     * Validation rules for bulk import users
     * 
     * @return array
     */
    public static function importRules()
    {
        return [
            'import_file' => [
                'label' => 'File Import',
                'rules' => 'uploaded[import_file]|ext_in[import_file,xlsx,xls]|max_size[import_file,5120]',
                'errors' => [
                    'uploaded' => 'File import harus dipilih',
                    'ext_in' => 'Format file harus XLSX atau XLS',
                    'max_size' => 'Ukuran file maksimal 5MB'
                ]
            ]
        ];
    }

    /**
     * Custom validation to check if old password is correct
     * 
     * @param string $oldPassword
     * @param string $error
     * @param array $data
     * @return bool
     */
    public static function validateOldPassword($oldPassword, &$error, $data)
    {
        $userModel = new \App\Models\UserModel();
        $userId = session()->get('user_id');

        if (!$userId) {
            $error = 'Sesi tidak valid';
            return false;
        }

        $user = $userModel->find($userId);

        if (!$user) {
            $error = 'User tidak ditemukan';
            return false;
        }

        if (!password_verify($oldPassword, $user['password_hash'])) {
            $error = 'Password lama tidak sesuai';
            return false;
        }

        return true;
    }

    /**
     * Sanitize user input data
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

        // Remove password_confirm if exists
        if (isset($sanitized['password_confirm'])) {
            unset($sanitized['password_confirm']);
        }

        // Remove new_password_confirm if exists
        if (isset($sanitized['new_password_confirm'])) {
            unset($sanitized['new_password_confirm']);
        }

        // Set default value for is_active if not provided
        if (!isset($sanitized['is_active'])) {
            $sanitized['is_active'] = 1;
        }

        return $sanitized;
    }
}
