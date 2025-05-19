<?php

namespace App\Repositories;

use App\Enums\CasePriority;
use App\Enums\TestCaseStatus;
use App\Interfaces\TestResultsRepositoryInterface;
use App\Models\TestResults;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TestResultsRepository implements TestResultsRepositoryInterface
{

    /**
     * @param $offset starts from 0
     * @param $pageSize
     * @return mixed
     */
    public function index($offset = 0, $pageSize = -1, $sort='start_time', $order='desc', $search='', $filter='', $testRunId = null)
    {

        $query = TestResults::select(['tr.id','tr.test_case_id','tr.test_run_id','tr.status','tr.failed_step',
            'tr.tc_params',
            'tr.error_message','tr.full_error','tr.start_time','tr.execution_time','tr.screenshot_path',
            'tr.script_name_id','rerun_id', 'script_names.script_name as script_name',
            'tc.title as tc_name','trr.issues','trr.reviewer_id','trr.status as review_status','tc.priority as priority'])
            ->leftJoin('script_names', 'script_names.id', '=', 'tr.script_name_id')
            ->leftJoin('test_cases as tc', 'tc.id', '=', 'tr.test_case_id')
            ->leftJoin('test_result_reviews as trr', 'trr.review_test_result_id', '=', 'tr.id')
            ->leftJoin('test_runs', 'test_runs.id', '=', 'tr.test_run_id')
            ->from('test_results as tr')
            ->where('test_runs.is_automation', '=', 1);

        if (!empty($sort) && in_array($sort, ['start_time', 'execution_time', 'priority', 'status', 'reviewer_id', 'review_status'])) {
            $query->orderBy($sort, $order);
        }

        if ($testRunId != null) {
            $query->where('test_runs.id', '=', $testRunId);
        }

        if (!empty($filter)) {
            $validator = Validator::make($filter, [
                // 'status' => [Rule::in(['Passed', 'Failed', 'Skipped'])],
                // 'priority' => [Rule::in(['P1', 'P2', 'P3', 'P4', 'P5'])],
                'status' => 'integer',
                'priority' => 'integer|between:1,5',
            ]);

            if ($validator->valid()) {
                if (!empty($filter['status'])) {
                    // $query->where('status', '=', TestCaseStatus::fromName($filter['status']));
                    $query->where('tr.status', '=', TestCaseStatus::from($filter['status']));
                }
                if (!empty($filter['priority'])) {
                    // $query->where('priority', '=', CasePriority::fromGridValue($filter['priority'])->value);
                    $query->where('priority', '=', CasePriority::from($filter['priority']));
                }
            }
        }

        if ($offset == 0 && $pageSize == -1)
            return $query->paginate(function($total) use ($pageSize) {
                    if($pageSize == -1){
                        return $total;
                    }
                    return $pageSize;
                }
            );
        else {
            $page = ($offset + $pageSize) / $pageSize;
            return $query->paginate($pageSize, ['*'], 'page', $page);
        }
    }

    public function getById($id)
    {
        return TestResults::findOrFail($id);
    }

    public function store(array $data)
    {
        $data['error_message_hash'] = $this->get64BitHash($data['error_message']);
        return TestResults::create($data);
    }

    public function update(array $data, $id)
    {
        $data['error_message_hash'] = $this->get64BitHash($data['error_message']);
        return TestResults::whereId($id)->update($data);
    }

    /**
     * @param $id
     * @return int count of deleted rows
     */
    public function delete($id) : int
    {
        return TestResults::destroy($id);
    }

    private function get64BitHash($str): string
    {
        return \gmp_strval(\gmp_init(substr(md5($str), 0, 16), 16), 10);
    }

    public function attachScreenshot($id, $path) {
        TestResults::where('id', $id)->update(['screenshot_path' => $path]);

        return $path;
    }

    public function attachTestLog($id, $htmlLog) {
        return TestResults::where('id', $id)->update(['log' => $htmlLog]);
    }

}
