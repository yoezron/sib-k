<?php

/**
 * File Path: app/Services/UserService.php
 * 
 * User Service
 * Business logic layer untuk User management
 * 
 * @package    SIB-K
 * @subpackage Services
 * @category   Business Logic
 * @author     Development Team
 * @created    2025-01-05
 */

namespace App\Services;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\StudentModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class UserService
{
    protected $userModel;
    protected $roleModel;
    protected $studentModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->studentModel = new StudentModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get all users with role information and pagination
     * 
     * @param array $filters
     * @param int $perPage
     * @return array
     */
    public function getAllUsers($filters = [], $perPage = 10)
    {
        $builder = $this->userModel->select('users.*, roles.role_name')
            ->join('roles', 'roles.id = users.role_id');

        // Apply filters
        if (!empty($filters['role_id'])) {
            $builder->where('users.role_id', $filters['role_id']);
        }

        if (!empty($filters['is_active'])) {
            $builder->where('users.is_active', $filters['is_active']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('users.username', $filters['search'])
                ->orLike('users.email', $filters['search'])
                ->orLike('users.full_name', $filters['search'])
                ->groupEnd();
        }

        // Order by
        $orderBy = $filters['order_by'] ?? 'users.created_at';
        $orderDir = $filters['order_dir'] ?? 'DESC';
        $builder->orderBy($orderBy, $orderDir);

        // Paginate
        return [
            'users' => $builder->paginate($perPage),
            'pager' => $this->userModel->pager,
        ];
    }

    /**
     * Get user by ID with full details
     * 
     * @param int $userId
     * @return array|null
     */
    public function getUserById($userId)
    {
        $user = $this->userModel->getUserWithRole($userId);

        if (!$user) {
            return null;
        }

        // Check if user is a student
        $student = $this->studentModel->where('user_id', $userId)->first();
        if ($student) {
            $user['is_student'] = true;
            $user['student_data'] = $student;
        } else {
            $user['is_student'] = false;
        }

        return $user;
    }

    /**
     * Create new user
     * 
     * @param array $data
     * @return array ['success' => bool, 'message' => string, 'user_id' => int|null]
     */
    public function createUser($data)
    {
        $this->db->transStart();

        try {
            // Prepare user data
            $userData = [
                'role_id' => $data['role_id'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'], // Will be hashed by UserModel callback
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'is_active' => $data['is_active'] ?? 1,
            ];

            // Insert user
            if (!$this->userModel->insert($userData)) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal menyimpan data user: ' . implode(', ', $this->userModel->errors()),
                    'user_id' => null,
                ];
            }

            $userId = $this->userModel->getInsertID();

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data',
                    'user_id' => null,
                ];
            }

            // Log activity
            $this->logActivity('create_user', $userId, "User baru dibuat: {$data['username']}");

            return [
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'user_id' => $userId,
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creating user: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'user_id' => null,
            ];
        }
    }

    /**
     * Update user data
     * 
     * @param int $userId
     * @param array $data
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateUser($userId, $data)
    {
        $this->db->transStart();

        try {
            // Check if user exists
            $user = $this->userModel->find($userId);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                ];
            }

            // Prepare update data
            $updateData = [
                'role_id' => $data['role_id'],
                'username' => $data['username'],
                'email' => $data['email'],
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'is_active' => $data['is_active'] ?? 1,
            ];

            // Update user
            if (!$this->userModel->update($userId, $updateData)) {
                $this->db->transRollback();
                return [
                    'success' => false,
                    'message' => 'Gagal mengupdate data user: ' . implode(', ', $this->userModel->errors()),
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
            $this->logActivity('update_user', $userId, "User diupdate: {$data['username']}");

            return [
                'success' => true,
                'message' => 'Data user berhasil diupdate',
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error updating user: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete user (soft delete)
     * 
     * @param int $userId
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteUser($userId)
    {
        try {
            // Check if user exists
            $user = $this->userModel->find($userId);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                ];
            }

            // Prevent deleting own account
            if ($userId == session()->get('user_id')) {
                return [
                    'success' => false,
                    'message' => 'Anda tidak dapat menghapus akun Anda sendiri',
                ];
            }

            // Check if user is a student with active data
            $student = $this->studentModel->where('user_id', $userId)
                ->where('status', 'Aktif')
                ->first();

            if ($student) {
                return [
                    'success' => false,
                    'message' => 'User tidak dapat dihapus karena masih terkait dengan data siswa aktif',
                ];
            }

            // Soft delete
            if (!$this->userModel->delete($userId)) {
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus user',
                ];
            }

            // Log activity
            $this->logActivity('delete_user', $userId, "User dihapus: {$user['username']}");

            return [
                'success' => true,
                'message' => 'User berhasil dihapus',
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error deleting user: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Toggle user active status
     * 
     * @param int $userId
     * @return array ['success' => bool, 'message' => string, 'is_active' => int]
     */
    public function toggleActive($userId)
    {
        try {
            // Check if user exists
            $user = $this->userModel->find($userId);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                    'is_active' => 0,
                ];
            }

            // Prevent deactivating own account
            if ($userId == session()->get('user_id')) {
                return [
                    'success' => false,
                    'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri',
                    'is_active' => $user['is_active'],
                ];
            }

            // Toggle status
            $newStatus = $user['is_active'] == 1 ? 0 : 1;

            if (!$this->userModel->update($userId, ['is_active' => $newStatus])) {
                return [
                    'success' => false,
                    'message' => 'Gagal mengubah status user',
                    'is_active' => $user['is_active'],
                ];
            }

            // Log activity
            $statusText = $newStatus == 1 ? 'diaktifkan' : 'dinonaktifkan';
            $this->logActivity('toggle_user_status', $userId, "User {$statusText}: {$user['username']}");

            return [
                'success' => true,
                'message' => 'Status user berhasil diubah',
                'is_active' => $newStatus,
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error toggling user status: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'is_active' => 0,
            ];
        }
    }

    /**
     * Change user password
     * 
     * @param int $userId
     * @param string $newPassword
     * @param string|null $oldPassword
     * @return array ['success' => bool, 'message' => string]
     */
    public function changePassword($userId, $newPassword, $oldPassword = null)
    {
        try {
            // Check if user exists
            $user = $this->userModel->find($userId);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                ];
            }

            // Verify old password if provided
            if ($oldPassword !== null) {
                if (!password_verify($oldPassword, $user['password_hash'])) {
                    return [
                        'success' => false,
                        'message' => 'Password lama tidak sesuai',
                    ];
                }
            }

            // Update password
            if (!$this->userModel->update($userId, ['password' => $newPassword])) {
                return [
                    'success' => false,
                    'message' => 'Gagal mengubah password',
                ];
            }

            // Log activity
            $this->logActivity('change_password', $userId, "Password diubah untuk user: {$user['username']}");

            return [
                'success' => true,
                'message' => 'Password berhasil diubah',
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error changing password: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Upload profile photo
     * 
     * @param int $userId
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     * @return array ['success' => bool, 'message' => string, 'file_path' => string|null]
     */
    public function uploadProfilePhoto($userId, $file)
    {
        try {
            // Check if user exists
            $user = $this->userModel->find($userId);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan',
                    'file_path' => null,
                ];
            }

            // Validate file
            if (!$file->isValid()) {
                return [
                    'success' => false,
                    'message' => 'File tidak valid',
                    'file_path' => null,
                ];
            }

            // Generate unique filename
            $newName = 'user_' . $userId . '_' . time() . '.' . $file->getExtension();

            // Move file to uploads/users directory
            $uploadPath = WRITEPATH . 'uploads/users/';

            // Create directory if not exists
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Move file
            if (!$file->move($uploadPath, $newName)) {
                return [
                    'success' => false,
                    'message' => 'Gagal mengupload file',
                    'file_path' => null,
                ];
            }

            // Delete old photo if exists
            if ($user['profile_photo']) {
                $oldPhotoPath = WRITEPATH . 'uploads/users/' . $user['profile_photo'];
                if (file_exists($oldPhotoPath)) {
                    @unlink($oldPhotoPath);
                }
            }

            // Update user profile photo
            if (!$this->userModel->update($userId, ['profile_photo' => $newName])) {
                return [
                    'success' => false,
                    'message' => 'Gagal menyimpan data foto',
                    'file_path' => null,
                ];
            }

            // Log activity
            $this->logActivity('upload_photo', $userId, "Foto profil diupload untuk user: {$user['username']}");

            return [
                'success' => true,
                'message' => 'Foto profil berhasil diupload',
                'file_path' => $newName,
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error uploading photo: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'file_path' => null,
            ];
        }
    }

    /**
     * Get user statistics
     * 
     * @return array
     */
    public function getUserStatistics()
    {
        $stats = [
            'total' => $this->userModel->countAllResults(false),
            'active' => $this->userModel->where('is_active', 1)->countAllResults(false),
            'inactive' => $this->userModel->where('is_active', 0)->countAllResults(false),
            'by_role' => [],
        ];

        // Get count by role
        $roleStats = $this->db->table('users')
            ->select('roles.role_name, COUNT(users.id) as total')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.deleted_at', null)
            ->groupBy('roles.id')
            ->get()
            ->getResultArray();

        foreach ($roleStats as $stat) {
            $stats['by_role'][$stat['role_name']] = (int) $stat['total'];
        }

        return $stats;
    }

    /**
     * Get all roles for dropdown
     * 
     * @return array
     */
    public function getRoles()
    {
        return $this->roleModel->orderBy('role_name', 'ASC')->findAll();
    }

    /**
     * Log user activity
     * 
     * @param string $action
     * @param int $targetUserId
     * @param string $description
     * @return void
     */
    private function logActivity($action, $targetUserId, $description)
    {
        // This can be expanded to save to activity_logs table
        log_message('info', "[UserService] Action: {$action}, Target User: {$targetUserId}, Description: {$description}");
    }
}
