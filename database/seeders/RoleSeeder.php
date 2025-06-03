<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::findOrCreate('super_admin');
        $gudang = Role::findOrCreate('gudang');
        $kasir = Role::findOrCreate('kasir');

        $gudang->givePermissionTo([
            'view_any_produk::mutasi',
            'view_produk::mutasi',
            'create_produk::mutasi',
            'update_produk::mutasi',
            'delete_produk::mutasi',
            'delete_any_produk::mutasi',
            'force_delete_produk::mutasi',
            'force_delete_any_produk::mutasi',
            'restore_produk::mutasi',
            'restore_any_produk::mutasi',
            'replicate_produk::mutasi',
            'reorder_produk::mutasi'
        ]);
    }
}
