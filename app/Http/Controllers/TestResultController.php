<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Enums\UserPermission;
use App\Http\Resources\TestResultsResource;
use App\Interfaces\TestResultsRepositoryInterface;
use App\Models\Project;
use App\Models\TestRun;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TestResultController extends Controller
{

    private TestResultsRepositoryInterface $testResultsRepository;

    public function __construct(TestResultsRepositoryInterface $testResultsRepositoryInterface)
    {
        $this->testResultsRepository = $testResultsRepositoryInterface;
    }

    public function index(Request $request, $test_run_id)
    {
        $testRun = TestRun::findOrFail($test_run_id);
        $project = Project::findOrFail($testRun->project_id);
        $projectActiveUsers = $project->activeUsers()->get()->sortBy('name');

        return view('test_result.list_page')
            ->with('testRun', $testRun)
            ->with('project', $project)
            ->with('projectActiveUsers', $projectActiveUsers)
            ->with('host', $request->schemeAndHttpHost());
    }

    public function list(Request $request, $test_run_id)
    {
        $testRun = TestRun::findOrFail($test_run_id);
        $offset = $request->input('offset') ?? 0;
        $pageSize = $request->input('limit') ?? -1;
        $sort = $request->input('sort') ?? 'start_time';
        $order = $request->input('order') ?? 'desc';
        $search = $request->input('search') ?? '';
        $filter = $request->input('filter') ?? '';
        $filter = json_decode($filter, true);

        $data = $this->testResultsRepository->index($offset, $pageSize, $sort, $order, $search, $filter, $testRun->id);
        if ($data == null) $data = [];

        return ApiResponseClass::sendTableResponse($data->total(), TestResultsResource::collection($data),'',200);
    }

    public function get_screenshot(Request $request, $id)
    {
        $tr = $this->testResultsRepository->getById($id);

        return response(Storage::disk('local')->get(Config::get('screenshots_dir'). DIRECTORY_SEPARATOR. $tr->screenshot_path))
            ->header('Content-Type', 'image/png');
    }

    public function get_testlog(Request $request, $id)
    {
        $tr = $this->testResultsRepository->getById($id);

        return response($tr->log)->header('Content-Type', 'text/html');
    }

    public function loadChart($test_run_id)
    {
        $testRun = TestRun::findOrFail($test_run_id);

        return view('test_result.chart')
            ->with('testRun', $testRun);
    }

    public function statistics($test_run_id)
    {
        $testRun = TestRun::findOrFail($test_run_id);
        $project = Project::findOrFail($testRun->project_id);

        return view('test_result.statistics')
            ->with('testRun', $testRun)
            ->with('project', $project);
    }

}