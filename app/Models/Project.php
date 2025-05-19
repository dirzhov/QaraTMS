<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @mixin IdeHelperProject
 */
class Project extends Model
{
    public function repositories()
    {
        return $this->hasMany(Repository::class, 'project_id', 'id');
    }

    public function testPlans()
    {
        return $this->hasMany(TestPlan::class, 'project_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'project_id', 'id');
    }

    public function activeUsers(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, UserProject::class,
            'project_id', 'id', 'id', 'user_id');
    }


    // Count Methods

    public function repositoriesCount()
    {
        return $this->repositories->count();
    }

    public function suitesCount()
    {
        $repositoryIds = Repository::where('project_id', $this->id)->pluck('id')->toArray();
        return Suite::whereIn('repository_id', $repositoryIds)->count();
    }

    public function casesCount()
    {
        $repositoryIds = Repository::where('project_id', $this->id)->pluck('id')->toArray();
        $suiteIds = Suite::whereIn('repository_id', $repositoryIds)->pluck('id')->toArray();
        return TestCase::whereIn('suite_id', $suiteIds)->count();
    }

    public function automatedCasesCount()
    {
        $repositoryIds = Repository::where('project_id', $this->id)->pluck('id')->toArray();
        $suiteIds = Suite::whereIn('repository_id', $repositoryIds)->pluck('id')->toArray();
        return TestCase::whereIn('suite_id', $suiteIds)->where('automated', true)->count();
    }

    public function testPlansCount()
    {
        return TestPlan::where('project_id', $this->id)->count();
    }

    public function testRunsCount()
    {
        return TestRun::where('project_id', $this->id)->where('is_automation', 0)->count();
    }

    public function automationTestRunsCount()
    {
        return TestRun::where('project_id', $this->id)->where('is_automation', 1)->count();
    }

    public function documentsCount()
    {
        return Document::where('project_id', $this->id)->count();
    }

    public function getAutomationPercent()
    {

        $totalCases = $this->casesCount();
        $automatedCases = $this->automatedCasesCount();

        if ($totalCases <= 0 || $automatedCases <= 0) {
            return 0;
        }

        $result = ($automatedCases * 100) / $totalCases;
        return round($result, 1);

    }
}
