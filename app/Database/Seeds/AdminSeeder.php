<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $adminRole = $this->db->table('roles')->where('name', 'admin')->get()->getRow();
        
        $data = [
            'role_id' => $adminRole->id,
            'username' => 'admin',
            'email' => 'admin@sibk.sch.id',
            'password' => password_hash('admin123', PASSWORD_BCRYPT),
            'full_name' => 'Administrator',
            'phone' => '08123456789',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('users')->insert($data);
    }
}
