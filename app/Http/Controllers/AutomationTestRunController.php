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
        $testRuns = TestRun::where('t.project_id', $project->id)
            ->select(['t.*','users.name as creator'])
            ->from('test_runs as t')
            ->where('t.is_automation', 1)
            ->join('users', 'users.id', '=', 't.creator_id')
            ->orderBy('t.created_at', 'DESC')->get();

        return view('autotest_run.list_page')
            ->with('project', $project)
            ->with('testRuns', $testRuns);
    }

}