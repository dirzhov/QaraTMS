<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Enums\UserStatus;
use App\Models\Project;
use App\Models\User;
use App\Models\UserProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('users.list_page')
            ->with('users', $users);
    }

    public function create()
    {
        if (!auth()->user()->can(UserPermission::manage_users)) {
            abort(403);
        }

        $projects = Project::all();

        return view('users.create_page')
            ->with('projects', $projects);
    }

    public function edit($user_id)
    {
        if (!auth()->user()->can(UserPermission::manage_users)) {
            abort(403);
        }

        $user = User::findOrFail($user_id);
        $projects = Project::all();
        $user->projects = UserProject::userProjects($user->id);

        return view('users.edit_page')
            ->with('user', $user)
            ->with('projects', $projects);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can(UserPermission::manage_users)) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:60',
            'last_name' => 'nullable|string|max:60',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'projects' => 'required|exists:projects,id',
            'status' => ['required',Rule::in(UserStatus::values())],
        ]);

        $newUser = User::create([
            'name' => $request->name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status
        ]);

        $this->setPermissions($request, $newUser);
        $this->assignProjects($request->projects, $newUser);

        return redirect()->route('users_list_page');
    }


    public function update(Request $request)
    {
        if (!auth()->user()->can(UserPermission::manage_users)) {
            abort(403);
        }

        $user = User::findOrFail($request->user_id);

        $request->validate([
            'name' => 'required',
            'first_name' => 'nullable|string|max:60',
            'last_name' => 'nullable|string|max:60',
            'email' => 'required|unique:users,email,'.$user->id,
            'projects' => 'required|exists:projects,id',
            'status' => [Rule::in(UserStatus::values())],
        ]);


        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $this->setPermissions($request, $user);
        $this->assignProjects($request->projects, $user);

        $user->projects = $request->projects;

        return redirect()->route('users_list_page');
    }

    public function destroy(Request $request)
    {
        if (!auth()->user()->can(UserPermission::manage_users)) {
            abort(403);
        }

        $user = User::findOrFail($request->user_id);
        $user->delete();
        return redirect()->route('users_list_page');
    }


    private function assignProjects($projects, $user): void {
        foreach ($projects as $project_id) {
            UserProject::updateOrCreate([
                'user_id' => $user->id,
                'project_id' => $project_id
            ],[
                'status' => 1
            ]);
        }

        UserProject::where("user_id", $user->id)->whereNotIn("project_id", $projects)->update(['status' => 0]);

    }

    private function setPermissions($request, $user)
    {

        // PROJECTS
        if ($request->add_edit_projects) {
            $user->givePermissionTo(UserPermission::add_edit_projects);
        } else {
            $user->revokePermissionTo(UserPermission::add_edit_projects);
        }

        if ($request->delete_projects) {
            $user->givePermissionTo(UserPermission::delete_projects);
        } else {
            $user->revokePermissionTo(UserPermission::delete_projects);
        }

        // REPOSITORIES
        if ($request->add_edit_repositories) {
            $user->givePermissionTo(UserPermission::add_edit_repositories);
        } else {
            $user->revokePermissionTo(UserPermission::add_edit_repositories);
        }

        if ($request->delete_repositories) {
            $user->givePermissionTo(UserPermission::delete_repositories);
        } else {
            $user->revokePermissionTo(UserPermission::delete_repositories);
        }

        // TEST SUITES
        if ($request->add_edit_test_suites) {
            $user->givePermissionTo(UserPermission::add_edit_test_suites);
        } else {
            $user->revokePermissionTo(UserPermission::add_edit_test_suites);
        }

        if ($request->delete_test_suites) {
            $user->givePermissionTo(UserPermission::delete_test_suites);
        } else {
            $user->revokePermissionTo(UserPermission::delete_test_suites);
        }

        // TEST CASES
        if ($request->add_edit_test_cases) {
            $user->givePermissionTo(UserPermission::add_edit_test_cases);
        } else {
            $user->revokePermissionTo(UserPermission::add_edit_test_cases);
        }

        if ($request->delete_test_cases) {
            $user->givePermissionTo(UserPermission::delete_test_cases);
        } else {
            $user->revokePermissionTo(UserPermission::delete_test_cases);
        }

        // USERS
        if ($request->manage_users) {
            $user->givePermissionTo(UserPermission::manage_users);
        } else {
            $user->revokePermissionTo(UserPermission::manage_users);
        }

        // TEST PLANS
        if ($request->add_edit_test_plans) {
            $user->givePermissionTo(UserPermission::add_edit_test_plans);
        } else {
            $user->revokePermissionTo(UserPermission::add_edit_test_plans);
        }

        if ($request->delete_test_plans) {
            $user->givePermissionTo(UserPermission::delete_test_plans);
        } else {
            $user->revokePermissionTo(UserPermission::delete_test_plans);
        }

        // TEST RUNS
        if ($request->add_edit_test_runs) {
            $user->givePermissionTo(UserPermission::add_edit_test_runs);
        } else {
            $user->revokePermissionTo(UserPermission::add_edit_test_runs);
        }

        if ($request->delete_test_runs) {
            $user->givePermissionTo(UserPermission::delete_test_runs);
        } else {
            $user->revokePermissionTo(UserPermission::delete_test_runs);
        }

        // DOCUMENTS
        if ($request->add_edit_documents) {
            $user->givePermissionTo(UserPermission::add_edit_documents);
        } else {
            $user->revokePermissionTo(UserPermission::add_edit_documents);
        }

        if ($request->delete_documents) {
            $user->givePermissionTo(UserPermission::delete_documents);
        } else {
            $user->revokePermissionTo(UserPermission::delete_documents);
        }

        if ($request->view_automation_runs) {
            $user->givePermissionTo(UserPermission::view_automation_runs);
        } else {
            $user->revokePermissionTo(UserPermission::view_automation_runs);
        }

        if ($request->manage_automation_runs) {
            $user->givePermissionTo(UserPermission::manage_automation_runs);
        } else {
            $user->revokePermissionTo(UserPermission::manage_automation_runs);
        }
    }
}
