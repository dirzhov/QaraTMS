<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVersion extends Model
{
    public static function allActive($projectId)
    {
        return ProductVersion::where([['status', '=', 1],['project_id', '=', $projectId]])
            ->orderBy('created_at', 'DESC')->get();
    }

}