<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Models\User;
use App\Models\Project;
use App\Models\Repository;
use App\Models\Suite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class RepositoryController extends Controller
{
    private User $creator;

    public function useHooks()
    {
        $this->beforeCalling(['store', 'update', 'create', 'edit'], function ($request, ...$params) {
            if (!auth()->user()->can(UserPermission::add_edit_repositories)) {
                return response(null, 403);
            }

            $this->creator = User::findOrFail(Auth::id());
        });
    }

    /*****************************************
     *  AJAX
     *****************************************/

    // RETURN [ { id: 1, parent_id: 0, title: "Branch 1", level: 1 }, {}, {} ],
    public function getSuitesTree($repository_id)
    {
        $repository = Repository::findOrFail($repository_id);
        $suitesTree = Suite::where('repository_id', $repository->id)->orderBy('order')->tree()->get()->toTree();

        $jsSuitesTree = [];

        foreach ($suitesTree as $suite) {
            $this->recursiveGetData($suite, $jsSuitesTree);
        }

        return $jsSuitesTree;
    }

    private function recursiveGetData($suite, &$jsSuitesTree)
    {
        $jsSuitesTree[] = [
            'id' => $suite->id,
            'level' => $suite->depth + 1,
            'parent_id' => $suite->parent_id,
            'title' => $suite->title
        ];

        foreach ($suite->children as $suiteChild) {
            $this->recursiveGetData($suiteChild, $jsSuitesTree);
        }
    }

    /*****************************************
     *  PAGES
     *****************************************/

    public function index($project_id)
    {
        $project = Project::findOrFail($project_id);
        $repositories = $project->repositories;

        return view('repository.list_page')
            ->with('project', $project)
            ->with('repositories', $repositories);
    }

    public function create($project_id)
    {
        $project = Project::findOrFail($project_id);
        return view('repository.create_page')
            ->with('project', $project);
    }

    public function show($project_id, $repository_id)
    {
        $project = Project::findOrFail($project_id);
        $repository = Repository::findOrFail($repository_id);
        $suitesTree = Suite::where('repository_id', $repository_id)->orderBy('order')->tree()->get()->toTree();

        $user = Auth::user();
        $canEditSuites = $user->can(UserPermission::add_edit_test_suites) == true ? 1 : 0;
        $canDeleteSuites = $user->can(UserPermission::delete_test_suites) == true ? 1 : 0;

        return view('repository.show_page')
            ->with('project', $project)
            ->with('repository', $repository)
            ->with('suitesTree', $suitesTree)
            ->with('canEditSuites', $canEditSuites)
            ->with('canDeleteSuites', $canDeleteSuites);
    }

    public function edit($project_id, $repository_id)
    {
        $project = Project::findOrFail($project_id);
        $repository = Repository::findOrFail($repository_id);

        return view('repository.edit_page')
            ->with('project', $project)
            ->with('repository', $repository);
    }

    /*****************************************
     *  CRUD
     *****************************************/

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'project_id' => 'required',
        ]);

        $repository = new Repository();

        $repository->title = $request->title;
        $repository->prefix = $request->prefix;
        $repository->project_id = $request->project_id;
        $repository->description = $request->description;
        $repository->creator_id = $this->creator->id;

        $repository->save();

        return redirect()->route('repository_list_page', $repository->project_id);
    }

    public function update(Request $request)
    {
        $repository = Repository::findOrFail($request->id);

        $repository->title = $request->title;
        $repository->prefix = $request->prefix;
        $repository->project_id = $request->project_id;
        $repository->description = $request->description;
        $repository->creator_id = $this->creator->id;

        $repository->save();

        return redirect()->route('repository_show_page', [$repository->project_id, $repository->id]);
    }

    public function destroy(Request $request)
    {
        if (!auth()->user()->can(UserPermission::delete_repositories)) {
            abort(403);
        }

        $repository = Repository::findOrFail($request->id);
        $repository->delete();
        return redirect()->route('repository_list_page', $request->project_id);
    }
}
