<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // User Management
            ['name' => 'view_users', 'display_name' => 'View Users', 'description' => 'Can view users', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'create_users', 'display_name' => 'Create Users', 'description' => 'Can create users', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'edit_users', 'display_name' => 'Edit Users', 'description' => 'Can edit users', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'description' => 'Can delete users', 'created_at' => date('Y-m-d H:i:s')],
            
            // Student Management
            ['name' => 'view_students', 'display_name' => 'View Students', 'description' => 'Can view students', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'create_students', 'display_name' => 'Create Students', 'description' => 'Can create students', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'edit_students', 'display_name' => 'Edit Students', 'description' => 'Can edit students', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'delete_students', 'display_name' => 'Delete Students', 'description' => 'Can delete students', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'import_students', 'display_name' => 'Import Students', 'description' => 'Can import students from Excel', 'created_at' => date('Y-m-d H:i:s')],
            
            // Counseling Sessions
            ['name' => 'view_sessions', 'display_name' => 'View Sessions', 'description' => 'Can view counseling sessions', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'create_sessions', 'display_name' => 'Create Sessions', 'description' => 'Can create counseling sessions', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'edit_sessions', 'display_name' => 'Edit Sessions', 'description' => 'Can edit counseling sessions', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'delete_sessions', 'display_name' => 'Delete Sessions', 'description' => 'Can delete counseling sessions', 'created_at' => date('Y-m-d H:i:s')],
            
            // Assessments
            ['name' => 'view_assessments', 'display_name' => 'View Assessments', 'description' => 'Can view assessments', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'create_assessments', 'display_name' => 'Create Assessments', 'description' => 'Can create assessments', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'take_assessments', 'display_name' => 'Take Assessments', 'description' => 'Can take assessments', 'created_at' => date('Y-m-d H:i:s')],
            
            // Reports
            ['name' => 'view_reports', 'display_name' => 'View Reports', 'description' => 'Can view reports', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'export_reports', 'display_name' => 'Export Reports', 'description' => 'Can export reports', 'created_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('permissions')->insertBatch($data);
        
        // Assign all permissions to admin role
        $adminRole = $this->db->table('roles')->where('name', 'admin')->get()->getRow();
        $permissions = $this->db->table('permissions')->get()->getResult();
        
        $rolePermissions = [];
        foreach ($permissions as $permission) {
            $rolePermissions[] = [
                'role_id' => $adminRole->id,
                'permission_id' => $permission->id,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }
        
        $this->db->table('role_permissions')->insertBatch($rolePermissions);
    }
}
