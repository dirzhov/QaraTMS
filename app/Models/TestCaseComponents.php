<?php

namespace App\Models;

use App\Enums\CommonStatus;
use Illuminate\Database\Eloquent\Model;

class TestCaseComponents extends Model
{
    public $timestamps = false;
    public static function tcComponents(int $testCaseId, CommonStatus|null $status = null)
    {
        $where[] = ['test_case_id', '=', $testCaseId];
        if ($status != null)
            $where[] = ['status', '=', $status->value];

        return TestCaseComponents::where($where)->get();
    }


}