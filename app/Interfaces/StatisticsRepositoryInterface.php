<?php

namespace App\Interfaces;

interface StatisticsRepositoryInterface
{
    public function getAllByGroups($testRunId);

    public function getAllByPriority($testRunId);

    public function getAllBySeverity($testRunId);

    public function getAllByDuration($testRunId);

    public function getAllAppDefects($testRunId);

    public function getAllTestDefects($testRunId);

    public function getDefectsOfGroup($testRunId, $group);

}