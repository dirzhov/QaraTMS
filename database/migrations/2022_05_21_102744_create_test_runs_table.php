<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestRunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_plan_id')->constrained('test_plans')->onDelete('cascade');
            $table->unsignedInteger('project_id');
            $table->string('title');
            $table->boolean('is_automation')->comment("it is automation run or manual");
            $table->boolean('for_local_run')->default(false)
                ->comment("indicate this run as for local execution for each developer, will not be included into general statistic");
            $table->tinyInteger('job_status')->default(0)
                ->comment("0 - not run, 1 - run, 2 - stopped, 3 - passed, 4 - failed");
            $table->unsignedInteger('job_id')->nullable(true)->comment("jenkins job id");
            $table->tinyInteger('environment')->comment("1-dev,2-container,3-staging,4-production");
            $table->string('os')->nullable()->comment("win,linux,ios");
            $table->string('browser')->nullable()->comment("for playwright: chrome,firefox,webkit");
            $table->string('device')->nullable()->comment("mobile,desktop,etc");
            $table->string('groups')->nullable()->comment("execute tests by selected groups");
            $table->string('priorities')->nullable()->comment("execute tests by selected priorities");
            $table->string('run_parameters')->nullable()->comment("addition parameters for run");
            $table->longText('data')->nullable();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_runs');
    }
}
