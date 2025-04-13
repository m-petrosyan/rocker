<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'admin' => [
                'all',
            ],
            'moderator' => [
                'all',
            ],
            'user' => [
            ],
        ];
        foreach ($permissions as $roleName => $perms) {
            $role = Role::query()->firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            foreach ($perms as $perm) {
                Permission::query()->firstOrCreate([
                    'name' => $perm,
                    'guard_name' => 'web',
                ]);

                $role->givePermissionTo($perm);
            }
        }
    }
}
