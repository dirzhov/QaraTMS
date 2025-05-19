<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutomationResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_case_id')->constrained('test_cases')->onDelete('cascade');
            $table->foreignId('test_run_id')->constrained('test_runs')->onDelete('cascade');
            $table->unsignedBigInteger("script_name_id")->nullable()->comment("id of script name");
            $table->char('tc_params', 254)->nullable(true)->comment("parameters and value from testng data provider");
            $table->integer("status")->nullable(false)->comment("status of TC: passed,failed,skipped");
            $table->integer("failed_step")->nullable()->comment("0 - failed on precondition, null - test passed");
            $table->text("error_message")->nullable()->comment("short error message, fails could be grouped by this message");
            $table->bigInteger("error_message_hash")->nullable()->comment("used for grouping");
            $table->text("full_error")->nullable()->comment("full error/exception message");
            $table->unsignedBigInteger("start_time", 6)->nullable(false)->comment("start time of test case");
            $table->float("execution_time")->nullable();
            $table->string("screenshot_path")->nullable();
            $table->unsignedBigInteger("rerun_id")->nullable()->comment("run_id related to current execution");
            $table->text("log")->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_results');
    }
}
