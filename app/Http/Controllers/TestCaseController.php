<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Enums\CommonStatus;
use App\Enums\UserPermission;
use App\Http\Requests\TestCaseRequest;
use App\Http\Resources\TestCaseResource;
use App\Models\Component;
use App\Models\Project;
use App\Models\Repository;
use App\Models\Suite;
use App\Models\TestCase;
use App\Models\TestCaseComponents;
use App\Models\TestResults;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TestCaseController extends Controller
{
    private User $creator;

    public function useHooks()
    {
        $this->beforeCalling(['store', 'update', 'clone'], function ($request, ...$params) {
            if (!auth()->user()->can(UserPermission::add_edit_test_cases)) {
                return response(null, 403);
            }
            $this->creator = User::findOrFail(Auth::id());
        });
    }

    public function store(TestCaseRequest $request)
    {
        $testCase = new TestCase();

        $testCase->title = $request->title;
        $testCase->requirements = $request->requirements;
        $testCase->automated = (bool) $request->automated;
        $testCase->automated_status = $request->automated_status;
        $testCase->script_name = $request->script_name;
        $testCase->priority = $request->priority;
        $testCase->severity = $request->severity;
        $testCase->suite_id = $request->suite_id;
        $testCase->order = $request->order;
        $testCase->data = $request->data;
        $testCase->creator_id = $this->creator->id;
        $testCase->assignee_id = $this->creator->id;

        $testCase->save();

        if (is_countable($request->components) && count($request->components) > 0) {
            foreach ($request->components as $component_id) {
                $tcc = new TestCaseComponents();
                $tcc->test_case_id = $testCase->id;
                $tcc->component_id = $component_id;
                $tcc->status = CommonStatus::ACTIVE;
                $tcc->save();
            }
        }

        $suite = Suite::findOrFail($testCase->suite_id);

        $testCase->repository_id = $suite->repository_id;  // это нужно для загрузки формы  read в js

        return [
            'html' => '',
            'json' => $testCase->toJson()
        ];
    }

    public function update(Request $request)
    {
        $testCase = TestCase::findOrFail($request->id);

        $testCase->title = $request->title;
        $testCase->requirements = $request->requirements;
        $testCase->automated = (bool) $request->automated;
        $testCase->automated_status = $request->automated_status;
        $testCase->script_name = $request->script_name;
        $testCase->priority = $request->priority;
        $testCase->severity = $request->severity;
        $testCase->suite_id = $request->suite_id;
        $testCase->data = $request->data;
        $testCase->assignee_id = $request->assignee;

        $testCase->save();

        $tcComponents = TestCaseComponents::tcComponents($testCase->id);
        foreach ($tcComponents as $tcComponent) {
            if (!in_array($tcComponent->component_id, $request->components)) {
                $tcComponent->status = CommonStatus::DELETED;
                $tcComponent->save();
            }
        }

        $tcComponents_ids = array_column($tcComponents->toArray(), 'component_id');
        if (is_countable($request->components) && count($request->components) > 0) {
            foreach ($request->components as $component_id) {
                if (in_array($component_id, $tcComponents_ids)) {
                    $tcc = $tcComponents->filter(function ($item, int $key) use ($component_id) {
                        return $item->component_id == $component_id && $item->status == CommonStatus::DELETED->value;
                    })->first();
                    if ($tcc != null) {
                        $tcc->status = CommonStatus::ACTIVE;
                        $tcc->save();
                    }
                } else {
                    $tcc = new TestCaseComponents();
                    $tcc->test_case_id = $testCase->id;
                    $tcc->component_id = $component_id;
                    $tcc->status = CommonStatus::ACTIVE;
                    $tcc->save();
                }
            }
        }

        $suite = Suite::findOrFail($testCase->suite_id);

        $testCase->repository_id = $suite->repository_id;  // это нужно для загрузки формы в js

        return [
            'html' => '',
            'json' => $testCase->toJson()
        ];
    }

    public function clone(Request $request)
    {
        DB::beginTransaction();
        try {
            $oldTestCase = TestCase::findOrFail($request->id);
            $testCase = $oldTestCase->replicate();

            $testCase->title = '(COPY) '.$testCase->title;

            if ($clone = TestCase::where('title', $testCase->title)->first()) {
                return ApiResponseClass::sendResponse(new TestCaseResource($clone),'Test Case already cloned',409, false);
            }
            $testCase->created_at = Carbon::now();
            $testCase->updated_at = Carbon::now();
            $testCase->order = $testCase->order + 1;

            $testCase->save();

            $tcComponents = TestCaseComponents::tcComponents($oldTestCase->id, CommonStatus::ACTIVE);
            foreach ($tcComponents as $tcComponent) {
                $newTcComponent = $tcComponent->replicate();
                $newTcComponent->test_case_id = $testCase->id;
                $newTcComponent->save();
            }

            $suite = Suite::findOrFail($testCase->suite_id);

            $testCase->repository_id = $suite->repository_id;  // это нужно для загрузки формы в js

            DB::commit();

            return ApiResponseClass::sendResponse(new TestCaseResource($testCase),'Test Case Created Successfully',201);
        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }


    public function destroy(Request $request)
    {
        if (!auth()->user()->can(UserPermission::delete_test_cases)) {
            abort(403);
        }

        $testCase = TestCase::findOrFail($request->id);
        $testCase->delete();
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->order as $data) {
            $testCase = TestCase::findOrFail($data['id']);
            $testCase->order = $data['order'];
            $testCase->save();
        }
    }

    /*****************************************
     *  PAGES / FORMS / HTML BLOCKS
     *****************************************/

    public function show($test_case_id)
    {
        $testCase = TestCase::findOrFail($test_case_id);
        $data = json_decode($testCase->data);

        $parentTestSuite = Suite::findOrFail($testCase->suite_id);
        $repository = Repository::findOrFail($parentTestSuite->repository_id);
        $project = Project::findOrFail($repository->project_id);
        $assignees = User::active();

        $dependedTestCase = null;
        if ($testCase->depended_tc_id != null)
            $dependedTestCase = TestCase::findOrFail($testCase->depended_tc_id);

        return view('test_case.show_page')
            ->with('project', $project)
            ->with('repository', $repository)
            ->with('parentTestSuite', $parentTestSuite)
            ->with('testCase', $testCase)
            ->with('data', $data)
            ->with('assignees', $assignees)
            ->with('dependedTestCase', $dependedTestCase);
    }

    public function loadCreateForm($repository_id, $parent_test_suite_id = null)
    {
        if ($parent_test_suite_id != null) {
            $parentTestSuite = Suite::where('id', $parent_test_suite_id)->first();
        } else {
            $parentTestSuite = Suite::where('repository_id', $repository_id)->first();
        }

        $repository = Repository::findOrFail($parentTestSuite->repository_id);
        $project = Project::findOrFail($repository->project_id);
        $assignees = User::active();
        $components = Component::activeComponents();

        return view('test_case.create_form')
            ->with('project', $project)
            ->with('repository', $repository)
            ->with('parentTestSuite', $parentTestSuite)
            ->with('assignees', $assignees)
            ->with('components', $components);
    }

    public function loadShowForm($test_case_id)
    {
        $testCase = TestCase::findOrFail($test_case_id);
        $data = json_decode($testCase->data);

        $parentTestSuite = Suite::findOrFail($testCase->suite_id);
        $repository = Repository::findOrFail($parentTestSuite->repository_id);
        $project = Project::findOrFail($repository->project_id);
        $assignees = User::active();
        $components = Component::activeComponents();

        $dependedTestCase = null;
        if ($testCase->depended_tc_id != null)
            $dependedTestCase = TestCase::findOrFail($testCase->depended_tc_id);

        return view('test_case.show_form')
            ->with('project', $project)
            ->with('repository', $repository)
            ->with('parentTestSuite', $parentTestSuite)
            ->with('testCase', $testCase)
            ->with('data', $data)
            ->with('assignees', $assignees)
            ->with('components', $components)
            ->with('dependedTestCase', $dependedTestCase);
    }

    public function loadShowOverlay($test_case_id)
    {
        $testCase = TestCase::findOrFail($test_case_id);
        $data = json_decode($testCase->data);

        $parentTestSuite = Suite::findOrFail($testCase->suite_id);
        $repository = Repository::findOrFail($parentTestSuite->repository_id);
        $project = Project::findOrFail($repository->project_id);
        $assignees = User::active();
        $components = Component::activeComponents();

        $dependedTestCase = null;
        if ($testCase->depended_tc_id != null)
            $dependedTestCase = TestCase::findOrFail($testCase->depended_tc_id);

        return view('test_case.show_overlay')
            ->with('project', $project)
            ->with('repository', $repository)
            ->with('parentTestSuite', $parentTestSuite)
            ->with('testCase', $testCase)
            ->with('data', $data)
            ->with('assignees', $assignees)
            ->with('components', $components)
            ->with('dependedTestCase', $dependedTestCase);
    }

    public function loadShowWithResultOverlay($test_result_id)
    {
        $testResult = TestResults::findOrFail($test_result_id);
        $testCase = TestCase::findOrFail($testResult->test_case_id);
        $data = json_decode($testCase->data);

        $parentTestSuite = Suite::findOrFail($testCase->suite_id);
        $repository = Repository::findOrFail($parentTestSuite->repository_id);
        $project = Project::findOrFail($repository->project_id);
        $assignees = User::active();
        $components = Component::activeComponents();

        $dependedTestCase = null;
        if ($testCase->depended_tc_id != null)
            $dependedTestCase = TestCase::findOrFail($testCase->depended_tc_id);

        return view('test_case.show_overlay')
            ->with('project', $project)
            ->with('repository', $repository)
            ->with('parentTestSuite', $parentTestSuite)
            ->with('testCase', $testCase)
            ->with('data', $data)
            ->with('assignees', $assignees)
            ->with('components', $components)
            ->with('failedStep', $testResult->failed_step)
            ->with('dependedTestCase', $dependedTestCase);
    }

    public function loadEditForm($test_case_id)
    {
        $testCase = TestCase::findOrFail($test_case_id);
        $data = json_decode($testCase->data);

        $parentTestSuite = Suite::findOrFail($testCase->suite_id);
        $repository = Repository::findOrFail($parentTestSuite->repository_id);
        $project = Project::findOrFail($repository->project_id);
        $assignees = User::active();
        $creator = User::findOrFail($testCase->creator_id);
        $components = Component::activeComponents();
        $testCase->components = array_column(TestCaseComponents::tcComponents($test_case_id, CommonStatus::ACTIVE)->toArray(), 'component_id');

        return view('test_case.edit_form')
            ->with('project', $project)
            ->with('repository', $repository)
            ->with('parentTestSuite', $parentTestSuite)
            ->with('testCase', $testCase)
            ->with('data', $data)
            ->with('assignees', $assignees)
            ->with('components', $components)
            ->with('creator', $creator);
    }

}
