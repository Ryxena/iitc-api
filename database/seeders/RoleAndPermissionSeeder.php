<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    private array $modules = [
        'Category', 'Competition', 'Team', 'Participant', 'Payment', 'Payment Status', 'User'
    ];

    private array $pluralActions = ['List'];

    private array $singularActions = [
        'View', 'Create', 'Update', 'Delete', 'Restore', 'Force Delete'
    ];

    public function run(): void
    {
        foreach ($this->modules as $module) {
            $plural = Str::plural($module);
            $singular = $module;

            foreach ($this->pluralActions as $action) {
                Permission::firstOrCreate([
                    'name' => "$action $plural",
                    'guard_name' => 'web',
                ]);
            }

            foreach ($this->singularActions as $action) {
                Permission::firstOrCreate([
                    'name' => "$action $singular",
                    'guard_name' => 'web',
                ]);
            }
        }
        Permission::firstOrCreate([
            'name' => "Detail Payment Team",
            'guard_name' => 'web',
        ]);

        Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);
        $permissions = [
            'Detail Payment Team',
            'List Teams',
            'Create Category',
            'Update Category',
            'Delete Category',
            'Create Competition',
            'Update Competition',
            'Delete Competition',
            'Update Payment Status',
            'List Users',
            'Delete User',
        ];
        $admin->givePermissionTo($permissions);

        $user = Role::firstOrCreate([
            'name' => 'User',
            'guard_name' => 'web',
        ]);
        $permissions = [
            'Create Team',
            'View Team',
            'Update Team',
            'Delete Team',
            'Create Payment',
        ];
        $user->givePermissionTo($permissions);
    }
}
