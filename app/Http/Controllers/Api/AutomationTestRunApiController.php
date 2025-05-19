<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponseClass;
use App\Enums\Environment;
use App\Http\Controllers\Controller;
use App\Http\Resources\TestRunResource;
use App\Models\Project;
use App\Models\TestRun;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutomationTestRunApiController extends Controller
{
    private User $creator;

    public function useHooks()
    {
        $this->beforeCalling(['index'], function ($request, ...$params) {
            $this->creator = User::findOrFail(Auth::id());
        });
    }

    /**
     * Observe test results of whole project
     *
     * @param $project_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $project_id)
    {
        $project = Project::findOrFail($project_id);

        $offset = $request->input('offset') ?? 0;
        $pageSize = $request->input('limit') ?? 15;
        $sort = $request->input('sort') ?? 'created_at';
        $order = $request->input('order') ?? 'desc';
        $filter = $request->input('filter') ?? '';
        $filter = json_decode($filter, true);

        $page = ($offset + $pageSize) / $pageSize;

        $testRuns = TestRun::where('project_id', $project->id)
            ->select(['test_runs.*','users.name as creator'])
            ->where('is_automation', 1)
            ->join('users', 'users.id', '=', 'test_runs.creator_id')
            ->orderBy($sort, $order)
            ->paginate($pageSize, ['*'], 'page', $page);

        $testRuns = TestRunResource::collection($testRuns)->map(function ($testRun) use ($project_id) {
            $testRun['status'] = $testRun->getChartData();
            $testRun['url'] = route('test_results_page', [$testRun->id]);
            $testRun->created_at = $testRun->created_at->format('d-m-y H:i');
            if ($testRun->environment > 0)
                $testRun->environment = Environment::from($testRun->environment)->name;
            return $testRun;
        })->all();

        return ApiResponseClass::sendResponse($testRuns,'Automation test runs',201);
    }


}