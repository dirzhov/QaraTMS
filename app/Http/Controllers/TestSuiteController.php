<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Models\Repository;
use App\Models\Suite;
use App\Models\TestCase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestSuiteController extends Controller
{
    private User $creator;

    public function useHooks()
    {
        $this->beforeCalling(['store', 'update', 'updateOrder', 'updateParent'], function ($request, ...$params) {
            if (!auth()->user()->can(UserPermission::add_edit_test_suites)) {
                return response(null, 403);
            }
            $this->creator = User::findOrFail(Auth::id());
        });
    }

    /******************************************
     *  AJAX and html elements
     *****************************************/

    public function updateParent(Request $request)
    {
        $testSuite = Suite::findOrFail($request->id);
        $testSuite->parent_id = $request->parent_id;
        $testSuite->save();
    }

    public function updateOrder(Request $request)
    {
         foreach ($request->order as $data) {
            $testSuite = Suite::findOrFail($data['id']);
            $testSuite->order = $data['order'];
            $testSuite->save();
        }
    }

    public function loadEditor($operation, $repository_id, $test_suite_id = null)
    {
        $repository = Repository::findOrFail($repository_id);
        $editableSuite = isset($test_suite_id) ? Suite::findOrFail($test_suite_id) : null;

        $suitesTree = Suite::where('repository_id', $repository_id)->tree()->get()->toTree();

        return view('test_suite.editor')
            ->with('operation', $operation)
            ->with('repository', $repository)
            ->with('editableSuite', $editableSuite)
            ->with('suitesTree', $suitesTree);
    }

    public function loadCasesList($test_suite_id)
    {
        $suite = Suite::findOrFail($test_suite_id);
        $repository = Repository::findOrFail($suite->repository_id);
        $testCases = TestCase::select('id', 'suite_id', 'title', 'automated', 'priority', 'order')->where('suite_id',
            $test_suite_id)->orderBy('order')->get();

        return view('repository.test_cases_list')
            ->with('testCases', $testCases)
            ->with('repository', $repository);
    }

    /******************************************
     *  CRUD
     *****************************************/

    public function store(Request $request)
    {
        $suite = new Suite();

        $suite->repository_id = $request->repository_id;
        $suite->parent_id = $request->parent_id;
        $suite->title = $request->title;
        $suite->creator_id = $this->creator->id;
        $suite->save();

        return [
            'html' => '',
            'json' => $suite->toJson()
        ];
    }

    public function update(Request $request)
    {
        $testSuite = Suite::findOrFail($request->id);

        $testSuite->title = $request->title;
        $testSuite->creator_id = $this->creator->id;
        $testSuite->save();

        // TODO  - add move to other repository functionality
//        if($request->parent_id) {
//            $testSuite->parent_id = $request->parent_id;
//            $testSuite->save();
//            return redirect()->route('repository_show_page', [$request->project_id, $testSuite->repository_id]);
//        }
    }

    public function destroy(Request $request)
    {
        if (!auth()->user()->can('delete_test_suites')) {
            abort(403);
        }

        $testSuite = Suite::findOrFail($request->id);
        $testSuite->delete();
    }


}
