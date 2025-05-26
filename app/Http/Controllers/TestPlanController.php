<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Http\Requests\TestPlanRequest;
use App\Models\ProductVersion;
use App\Models\Project;
use App\Models\Repository;
use App\Models\Suite;
use App\Models\TestPlan;
use App\Models\TestRun;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestPlanController extends Controller
{
    private User $creator;

    public function useHooks()
    {
        $this->beforeCalling(['create', 'edit', 'store', 'update'], function ($request, ...$params) {
            if (!auth()->user()->can(UserPermission::add_edit_test_plans)) {
                return response(null, 403);
            }
            $this->creator = User::findOrFail(Auth::id());
        });
    }

    public function startNewTestRun($test_plan_id)
    {
        if (!auth()->user()->can(UserPermission::add_edit_test_runs)) {
            abort(403);
        }

        $testPlan = TestPlan::findOrFail($test_plan_id);

        $testRun = new TestRun();
        $testRun->title = 'Test Run';
        $testRun->test_plan_id = $testPlan->id;
        $testRun->project_id = $testPlan->project_id;
        $testRun->data = $testRun->getInitialData();
        $testRun->save();

        $testRun->title = 'Test Run '.$testRun->id.' for '.$testPlan->title;
        $testRun->save();

        return redirect()->route('test_run_show_page', [$testPlan->project_id, $testRun->id]);
    }

    /*****************************************
     *  PAGES
     *****************************************/

    public function index($project_id)
    {
        $project = Project::findOrFail($project_id);
        $testPlans = TestPlan::where('project_id', $project->id)->orderBy('created_at', 'DESC')->get();

        return view('test_plan.list_page')
            ->with('project', $project)
            ->with('testPlans', $testPlans);
    }

    public function create($project_id)
    {
        $project = Project::findOrFail($project_id);
        $repositories = $project->repositories;
        $productVersions = ProductVersion::allActive($project_id);

        return view('test_plan.create_page')
            ->with('project', $project)
            ->with('repositories', $repositories)
            ->with('productVersions', $productVersions);
    }

    public function edit($project_id, $test_plan_id)
    {
        $project = Project::findOrFail($project_id);
        $repositories = $project->repositories;
        $testPlan = TestPlan::findOrFail($test_plan_id);
        $testSuitesTree = Suite::where('repository_id',
            $testPlan->repository_id)->orderBy('order')->tree()->get()->toTree();
        $prefix = Repository::findOrFail($testPlan->repository_id)->prefix;
        $productVersions = ProductVersion::allActive($project_id);

        return view('test_plan.edit_page')
            ->with('project', $project)
            ->with('testPlan', $testPlan)
            ->with('repositories', $repositories)
            ->with('prefix', $prefix)
            ->with('testSuitesTree', $testSuitesTree)
            ->with('productVersions', $productVersions);
    }

    /*****************************************
     *  CRUD
     *****************************************/

    public function store(TestPlanRequest $request)
    {
        $testPlan = new TestPlan();

        $testPlan->title = $request->title;
        $testPlan->project_id = $request->project_id;
        $testPlan->repository_id = $request->repository_id;
        $testPlan->description = $request->description;
        $testPlan->data = $request->data;  // это строка с id выбранных тест кейсов - 1,2,3 etc
        $testPlan->type = $request->testing_type;
        $testPlan->version = $request->product_version;
        $testPlan->creator_id = $this->creator->id;

        $testPlan->save();

        return redirect()->route('test_plan_list_page', $request->project_id);
    }

    public function update(TestPlanRequest $request)
    {
        $testPlan = TestPlan::findOrFail($request->id);

        $testPlan->title = $request->title;
        $testPlan->description = $request->description;
        $testPlan->repository_id = $request->repository_id;
        $testPlan->data = $request->data;  // это строка с id выбранных тест кейсов - 1,2,3 etc
        $testPlan->type = $request->testing_type;
        $testPlan->version = $request->product_version;
        $testPlan->creator_id = $this->creator->id;

        $testPlan->save();

        return redirect()->route('test_plan_update_page', [$request->project_id, $request->id]);
    }

    public function destroy(TestPlanRequest $request)
    {
        if (!auth()->user()->can(UserPermission::delete_test_plans)) {
            abort(403);
        }

        $testPlan = TestPlan::findOrFail($request->id);
        $project_id = $testPlan->project_id;
        $testPlan->delete();
        return redirect()->route('test_plan_list_page', $project_id);
    }

    /*****************************************
     *  HTML js load
     *****************************************/

    public function loadRepoTree($repository_id)
    {
        $repository = Repository::findOrFail($repository_id);
        $project = Project::findOrFail($repository->project_id);
        $testSuitesTree = Suite::where('repository_id', $repository_id)->orderBy('order')->tree()->get()->toTree();

        return view('test_plan.tree')
            ->with('repository', $repository)
            ->with('prefix', $repository->prefix)
            ->with('testSuitesTree', $testSuitesTree)
            ->with('project', $project);
    }
}
