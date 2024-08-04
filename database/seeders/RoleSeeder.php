<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea ruoli
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'moderator']);
        Role::create(['name' => 'user']);

        // Crea autorizzazioni
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'edit articles']);

        // Assegna autorizzazioni ai ruoli
        $admin = Role::findByName('admin');
        $admin->givePermissionTo('manage users');
        $admin->givePermissionTo('edit articles');

        $moderator = Role::findByName('moderator');
        $moderator->givePermissionTo('edit articles');
    }
}
