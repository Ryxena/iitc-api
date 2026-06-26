<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EventPermissionSeeder extends Seeder
{
    private array $modules = [
        'Event',
    ];

    private array $pluralActions = ['List'];

    private array $singularActions = [
        'View', 'Create', 'Update', 'Delete', 'Restore', 'Force Delete'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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

        $permissions = [
            'Create Event',
            'Update Event',
            'Delete Event',
        ];
        $admin = Role::where('name', '=', 'Admin')->first();
        $admin->syncPermissions($permissions);
    }
}
