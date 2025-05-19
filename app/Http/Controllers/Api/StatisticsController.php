<?php

namespace App\Http\Controllers\Api;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Http\Resources\DefectsListResource;
use App\Http\Resources\TestResultsResource;
use App\Interfaces\StatisticsRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Mockery\Exception;

class StatisticsController extends Controller {

    private StatisticsRepositoryInterface $statisticsRepository;

    public function __construct(StatisticsRepositoryInterface $statisticsRepository) {
        $this->statisticsRepository = $statisticsRepository;
    }

    private function fetchJiraIssues($records, $request) {
        $all_issues = collect([]);
        foreach ($records as $i => $rec) {
            $issues = explode(',', $rec['issues']);
            foreach ($issues as $issue) {
                $all_issues->push($issue);
            }
        }

        $unique = $all_issues->unique();
        $queryIssues = $unique->values()->join(',');

        if (empty($queryIssues)) $queryIssues = '""';
        $url = Config::get('app.jira_host') . '/rest/api/2/search?jql=issuekey in ('. $queryIssues .')&maxResults=100&fields=summary,priority,status,issuetype';
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '. Config::get('app.jira_token'),
            'Content-Type' => 'application/json'
        ])->get($url);

        if ($response->getStatusCode() >= 500)
            throw new \Exception($response->getReasonPhrase(), $response->getStatusCode());

        $jiraIssues = $response->json();
        if ($jiraIssues && !isset($jiraIssues['errorMessages']))
            $totalJiraIssues = $jiraIssues['total'];
        else
            $totalJiraIssues = 0;

        $records = DefectsListResource::collection($records)->toArray($request);
        $data = [];

        if ($totalJiraIssues > 0)
            foreach ($jiraIssues['issues'] as $issue) {
                $i = [];
                $i['i_key'] = $issue['key'];
                $i['i_summary'] = $issue['fields']['summary'];
                $i['i_url'] = Config::get('app.jira_host').'/browse/'.$issue['key'];
                $i['i_type'] = $issue['fields']['issuetype']['name'];
                $i['priority'] = [
                    'id' => $issue['fields']['priority']['id'],
                    'name' => $issue['fields']['priority']['name'],
                    'iconUrl' => $issue['fields']['priority']['iconUrl'],
                ];
                $i['status'] = [
                    'id' => $issue['fields']['status']['id'],
                    'name' => $issue['fields']['status']['name'],
                    'iconUrl' => $issue['fields']['status']['iconUrl'],
                ];

                array_filter($records, function ($value) use (&$i, &$issue) {
                    if (str_contains($value['issues'], $issue['key'])) {
                        $i['tcs'][] = [
                            'id' => $value['id'],
                            'prefix' => $value['prefix'],
                            'priority' => $value['priority'],
                            'is_fixed' => $value['is_fixed']
                        ];
                        return true;
                    } else
                        return false;
                });

                $data[] = $i;
            }
        return $data;
    }
    public function get_all_app_defects(Request $request, $id)
    {
        $records = $this->statisticsRepository->getAllAppDefects($id);
        // Log::info($page);
        try {
            $data = $this->fetchJiraIssues($records, $request);
            return ApiResponseClass::sendResponse($data, '', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'Possibly issues with JIRA: '.$e->getMessage(), $e->getCode());
        }
    }

    public function get_all_by_groups(Request $request, $id)
    {
        $records = $this->statisticsRepository->getAllByGroups($id)->toArray();

        $groups = array_column($records, 'suite');

        $data = [
            array_column($records, 'failed'),
            array_column($records, 'passed'),
            array_column($records, 'skipped'),
            array_column($records, 'is_fixed')
        ];

        $result = ['labels' => $groups, 'data' => $data];

        return ApiResponseClass::sendResponse($result, '',200);
    }

    public function get_all_by_priority(Request $request, $id)
    {
        $records = $this->statisticsRepository->getAllByPriority($id)->toArray();

        $priorities = [];
        for ($i = 1; $i <= 5; $i++) {
            $priorities[] = 'P'.$i;
        }

        $newRecords = [];
        for ($i = 1; $i <= 5; $i++) {
            $foundRecord = array_filter($records, function($v, $k) use ($i) {
                return (int)$v['priority'] == $i;
            }, ARRAY_FILTER_USE_BOTH);
            if (empty($foundRecord))
                $newRecords[$i-1] = ['priority' => $i, 'failed' => 0, 'passed' => 0, 'skipped' => 0, 'is_fixed' => 0];
            else
                $newRecords[$i-1] = array_pop($foundRecord);
        }

        $result = ['labels' => $priorities, 'data' => [
            array_column($newRecords, 'failed'),
            array_column($newRecords, 'passed'),
            array_column($newRecords, 'skipped'),
            array_column($newRecords, 'is_fixed')
        ]];

        return ApiResponseClass::sendResponse($result, '',200);
    }

    public function get_all_by_severity(Request $request, $id)
    {
        $records = $this->statisticsRepository->getAllBySeverity($id)->toArray();

        $severities = [];
        for ($i = 1; $i <= 5; $i++) {
            $severities[] = 'P'.$i;
        }

        $newRecords = [];
        for ($i = 1; $i <= 5; $i++) {
            $foundRecord = array_filter($records, function($v, $k) use ($i) {
                return (int)$v['severity'] == $i;
            }, ARRAY_FILTER_USE_BOTH);
            if (empty($foundRecord))
                $newRecords[$i-1] = ['severity' => $i, 'failed' => 0, 'passed' => 0, 'skipped' => 0, 'is_fixed' => 0];
            else
                $newRecords[$i-1] = array_pop($foundRecord);
        }

        $result = ['labels' => $severities, 'data' => [
            array_column($newRecords, 'failed'),
            array_column($newRecords, 'passed'),
            array_column($newRecords, 'skipped'),
            array_column($newRecords, 'is_fixed')
        ]];

        return ApiResponseClass::sendResponse($result, '',200);
    }

    public function get_all_by_duration(Request $request, $id)
    {
        $records = $this->statisticsRepository->getAllByDuration($id);
        return ApiResponseClass::sendResponse($records, '',200);

    }

    public function get_all_test_defects(Request $request, $id)
    {
        $records = $this->statisticsRepository->getAllTestDefects($id);
        return ApiResponseClass::sendResponse($records, '',200);
    }

    public function get_defects_of_group(Request $request, $id, $group)
    {
        $records = $this->statisticsRepository->getDefectsOfGroup($id, $group);
        try {
            $data = $this->fetchJiraIssues($records, $request);
            return ApiResponseClass::sendResponse($data, '', 200);
        } catch (\Exception $e) {
            return ApiResponseClass::sendResponse(null, 'Possibly issues with JIRA: '.$e->getMessage(), $e->getCode());
        }

        return ApiResponseClass::sendResponse($data, '',200);
    }

}