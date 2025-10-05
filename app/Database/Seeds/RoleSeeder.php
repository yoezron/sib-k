<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'System administrator with full access',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'koordinator_bk',
                'display_name' => 'Koordinator BK',
                'description' => 'BK coordinator who manages all counseling services',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'guru_bk',
                'display_name' => 'Guru BK',
                'description' => 'Counselor who provides counseling services',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'wali_kelas',
                'display_name' => 'Wali Kelas',
                'description' => 'Homeroom teacher who monitors student violations',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'orang_tua',
                'display_name' => 'Orang Tua',
                'description' => 'Parent who monitors child progress',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'siswa',
                'display_name' => 'Siswa',
                'description' => 'Student who accesses services and information',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('roles')->insertBatch($data);
    }
}
