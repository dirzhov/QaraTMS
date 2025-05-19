<?php

namespace Database\Seeders;

use App\Enums\UserPermission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class ApiUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUser = User::create([
            'id' => 2,
            'name' => 'api_user',
            'email' => 'api_user@admin.com',
            'password' => Hash::make('password'),
            'api_token' => '20|GvxneGUAQWwoGdwWyGAFrtZwAuJdxfHIFoELk2ktbfb277dd'
        ]);

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
