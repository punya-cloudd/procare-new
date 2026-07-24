<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Role & Permission
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            // Data Pengguna
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            // Dashboard
            'dashboard-view',

            // Data Master
            'unit-layanan-list',
            'unit-layanan-create',
            'unit-layanan-edit',
            'unit-layanan-delete',

            'dokter-list',
            'dokter-create',
            'dokter-edit',
            'dokter-delete',

            'petugas-list',
            'petugas-create',
            'petugas-edit',
            'petugas-delete',

            'jenis-penyakit-list',
            'jenis-penyakit-create',
            'jenis-penyakit-edit',
            'jenis-penyakit-delete',

            'master-makanan-list',
            'master-makanan-create',
            'master-makanan-edit',
            'master-makanan-delete',


            'peserta-list',
            'peserta-create',
            'peserta-edit',
            'peserta-delete',

            'pemeriksaan-list',
            'pemeriksaan-create',
            'pemeriksaan-edit',
            'pemeriksaan-delete',

            'homevisit-list',
            'homevisit-create',
            'homevisit-edit',
            'homevisit-delete',

            'monitoring-makanan-list',
            'monitoring-makanan-create',
            'monitoring-makanan-edit',
            'monitoring-makanan-delete',

            'bouchard-list',
            'bouchard-create',
            'bouchard-edit',
            'bouchard-delete',

            'menu-data-master',
            'menu-transaksional'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
