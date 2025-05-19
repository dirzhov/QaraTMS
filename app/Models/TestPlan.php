<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperTestPlan
 */
class TestPlan extends Model
{
    public static function fromProject(int $project_id) {
        return TestPlan::where('project_id', $project_id)->get();
    }

}
