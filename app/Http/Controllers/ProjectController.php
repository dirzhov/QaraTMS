<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Models\Project;
use App\Models\Repository;
use App\Models\TestRun;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    private User $creator;

    public function useHooks()
    {
        $this->beforeCalling(['store', 'update', 'create', 'edit'], function ($request, ...$params) {
            if (!auth()->user()->can(UserPermission::add_edit_projects)) {
                return response(null, 403);
            }

            $this->creator = User::findOrFail(Auth::id());
        });
    }

    /*****************************************
     *  PAGES
     *****************************************/

    public function index()
    {
        $projects = Project::all();
        return view('project.list_page')->with('projects', $projects);
    }

    public function create()
    {
        return view('project.create_page');
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        $testRuns = TestRun::where('project_id', $project->id)->orderBy('created_at', 'DESC')->get();
        $repositories = $project->repositories;

        return view('project.show_page')
            ->with('project', $project)
            ->with('testRuns', $testRuns)
            ->with('repositories', $repositories);
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        return view('project.edit_page')
            ->with('project', $project);
    }

    /*****************************************
     *  CRUD
     *****************************************/

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $project = new Project();

        $project->title = $request->title;
        $project->description = $request->description;
        $project->creator_id = $this->creator->id;

        $project->save();

        // create default test repository
        $repository = new Repository();
        $repository->project_id = $project->id;
        $repository->title = "Default";
        $repository->prefix = "D";
        $repository->description = "Default Test Repository. Test suites and test cases are located here";
        $repository->creator_id = $this->creator->id;

        $repository->save();

        return redirect()->route('project_show_page', $project->id);
    }

    public function update(Request $request)
    {
        $project = Project::findOrFail($request->id);

        $project->title = $request->title;
        $project->description = $request->description;
        $project->creator_id = $this->creator->id;

        $project->save();

        return redirect()->route('project_show_page', $project->id);
    }

    public function destroy(Request $request)
    {
        if (!auth()->user()->can(UserPermission::delete_projects)) {
            abort(403);
        }

        $project = Project::findOrFail($request->id);
        $project->delete();
        return redirect()->route('project_list_page');
    }

}
