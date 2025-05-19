<?php

namespace App\Models;

use App\Enums\TestRunCaseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


/**
 * @mixin IdeHelperTestRun
 */
class TestRun extends Model
{
    /*
     * Data format
     *
     * [case_id => status]
     */
    public function getResults()
    {
        return (array) json_decode($this->data);
    }

    /*
     * $results is array [case_id => status]
     */
    public function saveResults($results)
    {
        $this->data = json_encode($results);
        $this->save();
    }

    public function getInitialData()
    {
        $testPlan = TestPlan::findOrFail($this->test_plan_id);
        $testCasesIds = explode(',', $testPlan->data);

        $testRunData = [];

        foreach ($testCasesIds as $testCaseId) {
            $testRunData[$testCaseId] = (object) ['s' => TestRunCaseStatus::NOT_TESTED, 'i' => ''];
        }
        return json_encode($testRunData);
    }

    public function getAutomationChartData()
    {
        $total = DB::scalar("select count(id) from test_results where test_run_id=". $this->id);

        $totalTestCases = $total != 0 ? $total : 1;

        $passed = DB::scalar("select count(id) from test_results where status=1 AND test_run_id=". $this->id);
        $failed = DB::scalar("select count(id) from test_results where status=2 AND test_run_id=". $this->id);
        $skipped = DB::scalar("select count(id) from test_results where status=3 AND test_run_id=". $this->id);

        // [number, percent]
        $chartData = [
            'passed' => [$passed, (100 / $totalTestCases) * $passed],
            'failed' => [$failed, (100 / $totalTestCases) * $failed],
            'skipped' => [$skipped, (100 / $totalTestCases) * $skipped]
        ];

        return $chartData;
    }


    public function getChartData()
    {
        $results = $this->getResults();

        $totalTestCases = count($results) != 0 ? count($results) : 1;
        $passed = 0;
        $failed = 0;
        $blocked = 0;
        $notTested = 0;

        foreach ($results as $testCaseId => $tc) {
            $status = $tc->s;
            if ($status == TestRunCaseStatus::PASSED) {
                $passed++;
            } elseif ($status == TestRunCaseStatus::FAILED) {
                $failed++;
            } elseif ($status == TestRunCaseStatus::BLOCKED) {
                $blocked++;
            } elseif ($status == TestRunCaseStatus::NOT_TESTED) {
                $notTested++;
            }

        }

        // [number, percent]
        $chartData = [
            'passed' => [$passed, (100 / $totalTestCases) * $passed],
            'failed' => [$failed, (100 / $totalTestCases) * $failed],
            'blocked' => [$blocked, (100 / $totalTestCases) * $blocked],
            'not_tested' => [$notTested, (100 / $totalTestCases) * $notTested],
        ];

        return $chartData;
    }


    public function removeDeletedCasesFromResults()
    {
        $currentResults = $this->getResults();
        $planTestCasesIds = array_keys($this->getResults());

        $existingCasesIds = TestCase::whereIn('id', $planTestCasesIds)->get()->pluck('id')->toArray();

        foreach ($planTestCasesIds as $planTestCaseId) {

            if (false == in_array($planTestCaseId, $existingCasesIds)) {
                unset($currentResults[$planTestCaseId]);
            }
        }

        $this->saveResults($currentResults);
    }
}
