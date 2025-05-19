<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Models\Project;
use App\Models\Repository;
use App\Models\Suite;
use App\Models\TestCase;
use App\Models\TestPlan;
use App\Models\TestRun;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestRunController extends Controller
{
    private User $creator;

    public function useHooks()
    {
        $this->beforeCalling(['store', 'update', 'create', 'edit'], function ($request, ...$params) {
            if (!auth()->user()->can(UserPermission::add_edit_test_runs)) {
                return response(null, 403);
            }
            $this->creator = User::findOrFail(Auth::id());
        });
    }

    public function updateCaseStatus(Request $request)
    {
        $testRun = TestRun::findOrFail($request->test_run_id);
        $results = $testRun->getResults();
        $tcData = (object)[];
        $tcData->s = $request->status;
        if (!empty($request->issues))
            $tcData->i = $request->issues;
        if (!empty($request->assignee))
            $tcData->a = $request->assignee;
        if (!empty($request->failed_step))
            $tcData->f = $request->failed_step;

        $results[$request->test_case_id] = $tcData;
        $testRun->saveResults($results);
    }

    /*****************************************
     *  PAGES
     *****************************************/

    public function index($project_id)
    {
        $project = Project::findOrFail($project_id);
        $testRuns = TestRun::where('project_id', $project->id)
            ->where('is_automation', 0)
            ->orderBy('created_at', 'DESC')->get();

        return view('test_run.list_page')
            ->with('project', $project)
            ->with('testRuns', $testRuns);
    }

    public function show($project_id, $test_run_id)
    {
        $project = Project::findOrFail($project_id);
        $testRun = TestRun::findOrFail($test_run_id);
        $testPlan = TestPlan::findOrFail($testRun->test_plan_id);
        $repository = Repository::findOrFail($testPlan->repository_id);
        $users = User::all(['id', 'name', 'status'])->keyBy('id');

        $testCasesIds = explode(',', $testPlan->data);
        $testSuitesIds = TestCase::whereIn('id', $testCasesIds)->get()->pluck('suite_id')->toArray();

        $testSuitesTree = Suite::whereIn('id', $testSuitesIds)->tree()->get()->toTree();
        $suites = Suite::whereIn('id', $testSuitesIds)->orderBy('order')->get();

        $testRun->removeDeletedCasesFromResults();

        $results = $testRun->getResults();

        return view('test_run.show_page')
            ->with('project', $project)
            ->with('testRun', $testRun)
            ->with('testPlan', $testPlan)
            ->with('repository', $repository)
            ->with('testSuitesTree', $testSuitesTree)
            ->with('suites', $suites)
            ->with('testCasesIds', $testCasesIds)
            ->with('results', $results)
            ->with('users', $users);
    }

    public function create($project_id, $is_automation)
    {
        $project = Project::findOrFail($project_id);
        $testPlans = TestPlan::fromProject($project_id);

        return view('test_run.create_page')
            ->with('project', $project)
            ->with('testPlans', $testPlans)
            ->with('is_automation', $is_automation);
    }

    public function edit($project_id, $test_run_id)
    {
        $project = Project::findOrFail($project_id);
        $testRun = TestRun::findOrFail($test_run_id);
        $testRun->priorities = explode(',',  $testRun->priorities);

        return view('test_run.edit_page')
            ->with('project', $project)
            ->with('testRun', $testRun);
    }


    /*****************************************
     *  CRUD
     *****************************************/

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'test_plan_id' => 'required',
            'environment' => 'integer|required',
            'priorities' => 'array',
        ]);

        $testRun = new TestRun();
        $testRun->title = $request->title;
        $testRun->test_plan_id = $request->test_plan_id;
        $testRun->project_id = $request->project_id;
        $testRun->data = $testRun->getInitialData();
        $testRun->creator_id = $this->creator->id;
        $testRun->environment = $request->environment;
        $testRun->os = $request->os;
        $testRun->browser = $request->browser;
        $testRun->device = $request->device;
        $testRun->run_parameters = $request->run_parameters;
        $testRun->groups = $request->groups;
        if (!empty($request->priorities))
            $testRun->priorities = join(',', $request->priorities);
        $testRun->is_automation = $request->is_automation;
        $testRun->save();

        if ($testRun->is_automation)
            return redirect()->route('autotest_run_list_page', $request->project_id);
        else
            return redirect()->route('test_run_list_page', $request->project_id);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'integer|required',
            'title' => 'required',
            'environment' => 'integer|required',
            'priorities' => 'array',
        ]);

        $testRun = TestRun::findOrFail($request->id);

        $testRun->title = $request->title;
        $testRun->creator_id = $this->creator->id;
        $testRun->environment = $request->environment;
        $testRun->os = $request->os;
        $testRun->browser = $request->browser;
        $testRun->device = $request->device;
        $testRun->run_parameters = $request->run_parameters;
        $testRun->groups = $request->groups;
        if (!empty($request->priorities))
            $testRun->priorities = join(',', $request->priorities);
        $testRun->is_automation = $request->is_automation;
        $testRun->save();

        return redirect()->route('test_run_show_page', [$testRun->project_id, $testRun->id]);
    }

    public function destroy(Request $request)
    {
        if (!auth()->user()->can(UserPermission::delete_test_runs)) {
            abort(403);
        }

        $testRun = TestRun::findOrFail($request->id);
        $testRun->delete();
        return redirect()->route('test_run_list_page', $request->project_id);
    }

    /*****************************************
     *  Test case load
     *****************************************/

    public function loadTestCase($test_run_id, $test_case_id)
    {
        $testRun = TestRun::findOrFail($test_run_id);
        $testRun->data = $testRun->getResults();

        $testCase = TestCase::findOrFail($test_case_id);
        $suite = Suite::findOrFail($testCase->suite_id);
        $repository = Repository::findOrFail($suite->repository_id);
        $data = json_decode($testCase->data);
        $assignees = Project::findOrFail($testRun->project_id)->activeUsers()->get();

        $dependedTestCase = null;
        if ($testCase->depended_tc_id != null)
            $dependedTestCase = TestCase::findOrFail($testCase->depended_tc_id);

        return view('test_run.test_case')
            ->with('repository', $repository)
            ->with('testCase', $testCase)
            ->with('testRun', $testRun)
            ->with('data', $data)
            ->with('assignees', $assignees)
            ->with('dependedTestCase', $dependedTestCase);
    }

    public function loadChart($test_run_id)
    {
        $testRun = TestRun::findOrFail($test_run_id);

        return view('test_run.chart')
            ->with('testRun', $testRun);
    }
}
