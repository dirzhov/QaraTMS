<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResults extends Model
{
    public $timestamps = false;

//    protected $casts = [
//        'start_time' => 'timestamp'
//    ];

    protected $guarded = ['script_name'];

}