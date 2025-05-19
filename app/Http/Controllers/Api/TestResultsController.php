<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponseClass;
use App\Classes\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestResultsRequest;
use App\Http\Resources\TestResultsResource;
use App\Interfaces\TestResultsRepositoryInterface;
use App\Models\TestResults;
use App\Models\ScriptName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class TestResultsController extends Controller
{
    private TestResultsRepositoryInterface $testResultsRepository;

    public function __construct(TestResultsRepositoryInterface $testResultsRepositoryInterface)
    {
        $this->testResultsRepository = $testResultsRepositoryInterface;
    }

    public function index(int $page, int $pageSize)
    {
        $data = $this->testResultsRepository->index($page, $pageSize);
        // Log::info($page);

        return ApiResponseClass::sendResponse(TestResultsResource::collection($data),'',200);
    }

    public function store(TestResultsRequest $request)
    {
        DB::beginTransaction();
        try{
            $scriptName = ScriptName::firstOrCreate(['script_name' => $request['script_name']]);
            $data = (new TestResultsResource($request))->toArray($request);
            if (!empty($scriptName))
                $data['script_name_id'] = $scriptName->id;

            $result = $this->testResultsRepository->store($data);

            DB::commit();
            return ApiResponseClass::sendResponse(new TestResultsResource($result),'Test result Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }

        return TestResults::create($request->validated());
    }

    public function show($test_result)
    {
        $result = $this->testResultsRepository->getById($test_result);
        $scriptName = ScriptName::find($result->script_name_id);
        $data = (new TestResultsResource($result))->toArray();
        if (!empty($scriptName))
            $data['script_name'] = $scriptName->script_name;

        return ApiResponseClass::sendResponse($data,'',200);
    }

    public function update(TestResultsRequest $request, $test_result)
    {
        DB::beginTransaction();
        try{
            $scriptName = ScriptName::firstOrCreate(['script_name' => $request['script_name']]);
            $data = (new TestResultsResource($request))->toArray($request);
            if (!empty($scriptName))
                $data['script_name_id'] = $scriptName->id;

            $isUpdated = $this->testResultsRepository->update($data, $test_result);

            DB::commit();
            return ApiResponseClass::sendResponse($data,
                $isUpdated ? 'Test result updated successful' : 'Test result is not updated',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    public function destroy(TestResultsRequest $request, $test_result)
    {
        $count = $this->testResultsRepository->delete($test_result);

        return ApiResponseClass::sendResponse(['deleted' => $count], 'Test result deleted successful',204);
    }

    public function upload_screenshot(Request $request)
    {
        $request->validate([
            'screenshot_png' => 'required|file|mimes:jpg,png|max:2048',
            'ids' => 'required|string'
        ]);

        $rids = $request->input('ids');
        $ids = explode(",", $rids);
        if (count($ids) == 0 && $ids[0] <= 0)
            return ApiResponseClass::sendResponse([], 'wrong ids',404);

        $hash = Hash::khash($ids[0]);
        $file = $request->file('screenshot_png');
        $path = $file->store(Config::get('screenshots_dir') . DIRECTORY_SEPARATOR
            . $hash[0].$hash[1].DIRECTORY_SEPARATOR.$hash[2].$hash[3]);
        Storage::setVisibility($path, 'public');
        $dbPath = substr($path, strlen(Config::get('screenshots_dir')));

        $savedIds = [];
        foreach ($ids as $id) {
            if (intval($id) > 0) {
                $this->testResultsRepository->attachScreenshot($id, $dbPath);
                $savedIds[] = $id;
            }
        }

        return ApiResponseClass::sendResponse(['path' => $path, "ids" => $savedIds], 'File uploaded successful',201);
    }

    public function upload_testlog(Request $request)
    {
        $request->validate([
            'log' => 'required|string',
            'ids' => 'required|string'
        ]);

        $ids = $request->input('ids');
        $htmlLog = $request->input('log');

        $savedIds = [];
        foreach (explode(",", $ids) as $id) {
            if (intval($id) > 0) {
                $this->testResultsRepository->attachTestLog($id, $htmlLog);
                $savedIds[] = $id;
            }
        }

        return ApiResponseClass::sendResponse(["ids" => $savedIds], 'Test log attached successfully',201);
    }

}