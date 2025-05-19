<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUser = User::create([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password')
        ]);

        Permission::create(['name' => UserPermission::manage_users]);

        Permission::create(['name' => UserPermission::add_edit_projects]);
        Permission::create(['name' => UserPermission::delete_projects]);

        Permission::create(['name' => UserPermission::add_edit_repositories]);
        Permission::create(['name' => UserPermission::delete_repositories]);

        Permission::create(['name' => UserPermission::add_edit_test_suites]);
        Permission::create(['name' => UserPermission::delete_test_suites]);

        Permission::create(['name' => UserPermission::add_edit_test_cases]);
        Permission::create(['name' => UserPermission::delete_test_cases]);

        Permission::create(['name' => UserPermission::add_edit_test_plans]);
        Permission::create(['name' => UserPermission::delete_test_plans]);

        Permission::create(['name' => UserPermission::add_edit_test_runs]);
        Permission::create(['name' => UserPermission::delete_test_runs]);

        Permission::create(['name' => UserPermission::add_edit_documents]);
        Permission::create(['name' => UserPermission::delete_documents]);

        Permission::create(['name' => UserPermission::view_automation_runs]);
        Permission::create(['name' => UserPermission::manage_automation_runs]);
        Permission::create(['name' => UserPermission::change_review_assignee]);


        $adminUser->givePermissionTo([
            UserPermission::manage_users,

            UserPermission::add_edit_projects,
            UserPermission::delete_projects,

            UserPermission::add_edit_repositories,
            UserPermission::delete_repositories,

            UserPermission::add_edit_test_suites,
            UserPermission::delete_test_suites,

            UserPermission::add_edit_test_cases,
            UserPermission::delete_test_cases,

            UserPermission::add_edit_test_plans,
            UserPermission::delete_test_plans,

            UserPermission::add_edit_test_runs,
            UserPermission::delete_test_runs,

            UserPermission::add_edit_documents,
            UserPermission::delete_documents,

            UserPermission::view_automation_runs,
            UserPermission::manage_automation_runs,
            UserPermission::change_review_assignee
        ]);
    }
}
