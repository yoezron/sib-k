<?php

/**
 * File Path: app/Models/UserModel.php
 * 
 * User Model
 * Mengelola data users/pengguna sistem dengan authentication & RBAC
 * 
 * @package    SIB-K
 * @subpackage Models
 * @category   Authentication
 * @author     Development Team
 * @created    2025-01-01
 */

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'role_id',
        'username',
        'email',
        'password_hash',
        'full_name',
        'phone',
        'profile_photo',
        'is_active',
        'last_login',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'role_id'    => 'required|integer|is_not_unique[roles.id]',
        'username'   => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]|alpha_numeric',
        'email'      => 'required|valid_email|max_length[255]|is_unique[users.email,id,{id}]',
        'password'   => 'required|min_length[6]|max_length[255]',
        'full_name'  => 'required|min_length[3]|max_length[255]',
        'phone'      => 'permit_empty|min_length[10]|max_length[20]|numeric',
        'is_active'  => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'username' => [
            'required'      => 'Username harus diisi',
            'min_length'    => 'Username minimal 3 karakter',
            'is_unique'     => 'Username sudah digunakan',
            'alpha_numeric' => 'Username hanya boleh alfanumerik',
        ],
        'email' => [
            'required'    => 'Email harus diisi',
            'valid_email' => 'Email tidak valid',
            'is_unique'   => 'Email sudah digunakan',
        ],
        'password' => [
            'required'   => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter',
        ],
        'full_name' => [
            'required'   => 'Nama lengkap harus diisi',
            'min_length' => 'Nama lengkap minimal 3 karakter',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
            unset($data['data']['password']);
        }

        return $data;
    }

    /**
     * Get user with role information
     * 
     * @param int $userId
     * @return array|null
     */
    public function getUserWithRole($userId)
    {
        return $this->select('users.*, roles.role_name, roles.description as role_description')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.id', $userId)
            ->first();
    }

    /**
     * Get user with role and permissions
     * 
     * @param int $userId
     * @return array|null
     */
    public function getUserWithPermissions($userId)
    {
        $user = $this->getUserWithRole($userId);

        if (!$user) {
            return null;
        }

        $db = \Config\Database::connect();

        $permissions = $db->table('role_permissions')
            ->select('permissions.permission_name')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('role_permissions.role_id', $user['role_id'])
            ->get()
            ->getResultArray();

        $user['permissions'] = array_column($permissions, 'permission_name');

        return $user;
    }

    /**
     * Authenticate user
     * 
     * @param string $username
     * @param string $password
     * @return array|false
     */
    public function authenticate($username, $password)
    {
        $user = $this->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        if ($user['is_active'] != 1) {
            return false;
        }

        // Update last login
        $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        return $this->getUserWithPermissions($user['id']);
    }

    /**
     * Get all users with role name
     * 
     * @return array
     */
    public function getAllWithRole()
    {
        return $this->select('users.*, roles.role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->orderBy('users.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get users by role
     * 
     * @param int $roleId
     * @return array
     */
    public function getUsersByRole($roleId)
    {
        return $this->where('role_id', $roleId)
            ->where('is_active', 1)
            ->orderBy('full_name', 'ASC')
            ->findAll();
    }

    /**
     * Get users by role name
     * 
     * @param string $roleName
     * @return array
     */
    public function getUsersByRoleName($roleName)
    {
        return $this->select('users.*')
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.role_name', $roleName)
            ->where('users.is_active', 1)
            ->orderBy('users.full_name', 'ASC')
            ->findAll();
    }

    /**
     * Check if user has permission
     * 
     * @param int $userId
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission($userId, $permissionName)
    {
        $db = \Config\Database::connect();

        $count = $db->table('users')
            ->join('role_permissions', 'role_permissions.role_id = users.role_id')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('users.id', $userId)
            ->where('permissions.permission_name', $permissionName)
            ->countAllResults();

        return $count > 0;
    }

    /**
     * Update user profile
     * 
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateProfile($userId, array $data)
    {
        // Remove sensitive fields
        unset($data['password_hash']);
        unset($data['role_id']);

        return $this->update($userId, $data);
    }

    /**
     * Change password
     * 
     * @param int $userId
     * @param string $newPassword
     * @return bool
     */
    public function changePassword($userId, $newPassword)
    {
        return $this->update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);
    }

    /**
     * Activate/Deactivate user
     * 
     * @param int $userId
     * @param bool $status
     * @return bool
     */
    public function toggleActive($userId, $status = true)
    {
        return $this->update($userId, ['is_active' => $status ? 1 : 0]);
    }

    /**
     * Get user statistics by role
     * 
     * @return array
     */
    public function getUserStatistics()
    {
        $db = \Config\Database::connect();

        return $db->table('users')
            ->select('roles.role_name, COUNT(users.id) as total')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.deleted_at', null)
            ->groupBy('roles.id')
            ->get()
            ->getResultArray();
    }
}
