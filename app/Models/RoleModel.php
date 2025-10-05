<?php

/**
 * File Path: app/Models/RoleModel.php
 * 
 * Role Model
 * Mengelola data roles/peran pengguna dalam sistem
 * 
 * @package    SIB-K
 * @subpackage Models
 * @category   RBAC
 * @author     Development Team
 * @created    2025-01-01
 */

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'role_name',
        'description',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'role_name'   => 'required|min_length[3]|max_length[50]|is_unique[roles.role_name,id,{id}]',
        'description' => 'permit_empty|max_length[500]',
    ];

    protected $validationMessages = [
        'role_name' => [
            'required'   => 'Nama role harus diisi',
            'min_length' => 'Nama role minimal 3 karakter',
            'max_length' => 'Nama role maksimal 50 karakter',
            'is_unique'  => 'Nama role sudah digunakan',
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
     * Get role with permissions
     * 
     * @param int $roleId
     * @return array|null
     */
    public function getRoleWithPermissions($roleId)
    {
        $role = $this->find($roleId);

        if (!$role) {
            return null;
        }

        $db = \Config\Database::connect();

        $permissions = $db->table('role_permissions')
            ->select('permissions.*')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('role_permissions.role_id', $roleId)
            ->get()
            ->getResultArray();

        $role['permissions'] = $permissions;

        return $role;
    }

    /**
     * Get all roles with user count
     * 
     * @return array
     */
    public function getRolesWithUserCount()
    {
        $db = \Config\Database::connect();

        return $db->table($this->table)
            ->select('roles.*, COUNT(users.id) as user_count')
            ->join('users', 'users.role_id = roles.id', 'left')
            ->groupBy('roles.id')
            ->orderBy('roles.id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Assign permissions to role
     * 
     * @param int $roleId
     * @param array $permissionIds
     * @return bool
     */
    public function assignPermissions($roleId, array $permissionIds)
    {
        $db = \Config\Database::connect();

        // Delete existing permissions
        $db->table('role_permissions')
            ->where('role_id', $roleId)
            ->delete();

        // Insert new permissions
        if (!empty($permissionIds)) {
            $data = [];
            foreach ($permissionIds as $permissionId) {
                $data[] = [
                    'role_id'       => $roleId,
                    'permission_id' => $permissionId,
                    'created_at'    => date('Y-m-d H:i:s'),
                ];
            }

            return $db->table('role_permissions')->insertBatch($data);
        }

        return true;
    }

    /**
     * Check if role can be deleted
     * 
     * @param int $roleId
     * @return bool
     */
    public function canDelete($roleId)
    {
        $db = \Config\Database::connect();

        // Check if role has users
        $userCount = $db->table('users')
            ->where('role_id', $roleId)
            ->countAllResults();

        return $userCount === 0;
    }

    /**
     * Get role by name
     * 
     * @param string $roleName
     * @return array|null
     */
    public function getRoleByName($roleName)
    {
        return $this->where('role_name', $roleName)->first();
    }
}
