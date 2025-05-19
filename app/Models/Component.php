<?php

namespace App\Models;

use App\Enums\CommonStatus;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    public static function activeComponents()
    {
        return Component::where('status', CommonStatus::ACTIVE)->get();
    }


}