<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $modules = [
            'Event',
        ];

        $pluralActions = ['List'];

        $singularActions = [
            'View', 'Create', 'Update', 'Delete', 'Restore', 'Force Delete'
        ];

        foreach ($modules as $module) {
            $plural = Str::plural($module);
            $singular = $module;

            foreach ($pluralActions as $action) {
                Permission::firstOrCreate([
                    'name' => "$action $plural",
                    'guard_name' => 'web',
                ]);
            }

            foreach ($singularActions as $action) {
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

        $admin = Role::firstOrCreate(
            ['name' => 'Admin']
        );

        if ($admin) {
            $admin->givePermissionTo($permissions);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // If you need to revert the changes, define how to do so
        $permissions = [
            'Create Event',
            'Update Event',
            'Delete Event',
        ];

        $admin = Role::where('name', '=', 'Admin')->first();
        if ($admin) {
            $admin->revokePermissionTo($permissions);
        }

        Permission::whereIn('name', [
            'Create Event',
            'Update Event',
            'Delete Event',
            'View Events',
            'Create Event',
            'Update Event',
            'Delete Event',
            'Restore Event',
            'Force Delete Event',
            'List Events'
        ])->delete();
    }
};
