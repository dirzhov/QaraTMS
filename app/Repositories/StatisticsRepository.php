<?php

namespace App\Repositories;

use App\Interfaces\StatisticsRepositoryInterface;
use App\Models\TestResults;
use Illuminate\Support\Facades\DB;

class StatisticsRepository implements StatisticsRepositoryInterface
{

    public function getAllByGroups($testRunId)
    {
        $query = TestResults::select(DB::raw('count(tc.id) as total, s.title as suite, count(trr.is_fixed) as is_fixed'.
            ', CAST(sum(if(tr.status=1, 1, 0)) as UNSIGNED) as passed'.
            ', CAST(sum(if(tr.status=2, 1, 0)) as UNSIGNED) as failed'.
            ', CAST(sum(if(tr.status=3, 1, 0)) as UNSIGNED) as skipped')
        )
            ->join('test_cases as tc', 'tc.id', '=', 'tr.test_case_id')
            ->join('suites as s', 's.id', '=', 'tc.suite_id')
            ->leftJoin('test_result_reviews as trr', 'trr.review_test_result_id', '=', 'tr.id')
            ->from('test_results as tr')
            ->where([['tr.test_run_id', '=', $testRunId]])
            ->groupBy('suite_id');
        return $query->get();
    }

    public function getAllByPriority($testRunId)
    {
        $query = TestResults::select(DB::raw('tc.priority, count(trr.is_fixed) as is_fixed'.
            ', CAST(sum(if(tr.status=1, 1, 0)) as UNSIGNED) as passed'.
            ', CAST(sum(if(tr.status=2, 1, 0)) as UNSIGNED) as failed'.
            ', CAST(sum(if(tr.status=3, 1, 0)) as UNSIGNED) as skipped')
        )
            ->join('test_cases as tc', 'tc.id', '=', 'tr.test_case_id')
            ->leftJoin('test_result_reviews as trr', 'trr.review_test_result_id', '=', 'tr.id')
            ->from('test_results as tr')
            ->where([['tr.test_run_id', '=', $testRunId]])
            ->groupBy('tc.priority');
        return $query->get();
    }

    public function getAllBySeverity($testRunId)
    {
        $query = TestResults::select(DB::raw('tc.severity, count(trr.is_fixed) as is_fixed'.
            ', CAST(sum(if(tr.status=1, 1, 0)) as UNSIGNED) as passed'.
            ', CAST(sum(if(tr.status=2, 1, 0)) as UNSIGNED) as failed'.
            ', CAST(sum(if(tr.status=3, 1, 0)) as UNSIGNED) as skipped')
        )
            ->join('test_cases as tc', 'tc.id', '=', 'tr.test_case_id')
            ->leftJoin('test_result_reviews as trr', 'trr.review_test_result_id', '=', 'tr.id')
            ->from('test_results as tr')
            ->where([['tr.test_run_id', '=', $testRunId]])
            ->groupBy('tc.severity');
        return $query->get();
    }

    public function getAllByDuration($testRunId)
    {
        $minmax = TestResults::select(DB::raw('min(tr.execution_time) as min_duration, max(tr.execution_time) as max_duration'))
            ->from('test_results as tr')
            ->where('tr.test_run_id', '=', $testRunId)->first()->toArray();

        $labels = array();
        $query = "";
        $j = 1;
        $count = 10;
        $delta = round(($minmax['max_duration'] - $minmax['min_duration']) / $count);
        for ($i = $minmax['min_duration']; $i <= $minmax['max_duration']; $i=$i+$delta) {
            if ($i != $minmax['min_duration'])
                $query .= ', ';
            $query = $query. 'CAST(sum(if(tr.execution_time >= '.$i.' AND tr.execution_time < '. ($i + $delta).', 1,0)) as UNSIGNED) as d'.$i;
            $labels[] = $i+$delta;
        }

        $query = TestResults::select(DB::raw($query))
            ->from('test_results as tr')
            ->where('tr.test_run_id', '=', $testRunId);

        $arr = $query->first()->toArray();
        $result['data'] = array_values($arr);
        $result['labels'] = $labels;
        $result['min'] = $minmax['min_duration'];
        $result['max'] = $minmax['max_duration'];

        return $result;
    }

    public function getAllAppDefects($testRunId)
    {
        $query = TestResults::select(['tr.status','tc.id as tc_id','r.prefix','tc.priority as tc_priority',
            'trr.is_fixed as tc_is_fixed','trr.issues'])
            ->leftJoin('test_cases as tc', 'tc.id', '=', 'tr.test_case_id')
            ->leftJoin('test_result_reviews as trr', 'trr.review_test_result_id', '=', 'tr.id')
            ->leftJoin('test_runs as t_run', 't_run.id', '=', 'tr.test_run_id')
            ->join('suites', 'tc.suite_id', '=', 'suites.id')
            ->join('repositories as r', 'suites.repository_id', '=', 'r.id')
            ->from('test_results as tr')
            ->where([['t_run.id', '=', $testRunId],['trr.issues', 'IS NOT', null]]);

        return $query->get();
    }

    public function getAllTestDefects($testRunId)
    {
        // TODO: Implement getAllTestDefects() method.
    }

    public function getDefectsOfGroup($testRunId, $group)
    {
        $query = TestResults::select(['tr.status','tc.id as tc_id','r.prefix','tc.priority as tc_priority',
            'trr.is_fixed as tc_is_fixed','trr.issues'])
            ->leftJoin('test_cases as tc', 'tc.id', '=', 'tr.test_case_id')
            ->leftJoin('test_result_reviews as trr', 'trr.review_test_result_id', '=', 'tr.id')
            ->leftJoin('test_runs as t_run', 't_run.id', '=', 'tr.test_run_id')
            ->join('suites', 'tc.suite_id', '=', 'suites.id')
            ->join('repositories as r', 'suites.repository_id', '=', 'r.id')
            ->from('test_results as tr')
            ->where([['t_run.id', '=', $testRunId],['suites.title','=',$group],['trr.issues', 'IS NOT', null]]);

        return $query->get();
    }
}