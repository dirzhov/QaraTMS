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

        $testRuns = TestRun::where('t.project_id', $project->id)
            ->select(['t.*', 'users.name as creator', 'v.id as version_id', 'v.name as version', 't.job_status'])
            ->from('test_runs as t')
            ->where('t.is_automation', 1)
            ->join('users', 'users.id', '=', 't.creator_id')
            ->join('test_plans as tp', 'tp.id', '=', 't.test_plan_id')
            ->leftJoin('product_versions as v', 'v.id', '=', 'tp.version')
            ->orderBy($sort, $order)
            ->paginate($pageSize, ['*'], 'page', $page);

        $testRuns = TestRunResource::collection($testRuns)->map(function ($testRun) {
            $testRun['status'] = $testRun->getChartData();
            $testRun['url'] = route('test_results_page', [$testRun->id]);
            $testRun->created_at = $testRun->created_at->format('d-m-y H:i');
            if ($testRun->environment > 0)
                $testRun->environment = Environment::from($testRun->environment)->name;

            unset($testRun->data);

            return $testRun;
        })->toArray();

        return ApiResponseClass::sendResponse($testRuns,'Automation test runs',201);
    }


}