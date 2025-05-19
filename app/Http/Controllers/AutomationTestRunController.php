<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TestRun;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AutomationTestRunController extends Controller
{

    public function index($project_id)
    {
        $project = Project::findOrFail($project_id);
        $testRuns = TestRun::where('project_id', $project->id)
            ->select(['test_runs.*','users.name as creator'])
            ->where('is_automation', 1)
            ->join('users', 'users.id', '=', 'test_runs.creator_id')
            ->orderBy('created_at', 'DESC')->get();

        return view('autotest_run.list_page')
            ->with('project', $project)
            ->with('testRuns', $testRuns);
    }

}