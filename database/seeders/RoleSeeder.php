<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role_id' => 1, 'nama_role' => 'Admin', 'deskripsi_role' => 'Administrator'],
            ['role_id' => 2, 'nama_role' => 'Supervisor', 'deskripsi_role' => 'Supervisor'],
            ['role_id' => 3, 'nama_role' => 'Beauty Consultant', 'deskripsi_role' => 'Beauty Consultant'],
            ['role_id' => 4, 'nama_role' => 'Sales Promotion Girl', 'deskripsi_role' => 'Sales Promotion Girl'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['role_id' => $role['role_id']],
                [
                    'nama_role' => $role['nama_role'],
                    'deskripsi_role' => $role['deskripsi_role'],
                ]
            );
        }
    }
}
