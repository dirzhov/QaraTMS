<?php

namespace Database\Seeders;

use App\Enums\UserPermission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class AutomationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUser = User::create([
            'name' => 'Automation',
            'email' => 'automation@admin.com',
            'password' => Hash::make('password')
        ]);


        $adminUser->givePermissionTo([
            UserPermission::add_edit_test_runs,
            UserPermission::view_automation_runs,
            UserPermission::manage_automation_runs
        ]);
    }
}
