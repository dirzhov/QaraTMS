<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProject extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['user_id', 'project_id', 'status'];


    public static function userProjects($user_id) {
        return UserProject::where('user_id', $user_id)->where('status', 1)->pluck('project_id')->all();
    }

}
