<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suite_id')->constrained('suites')->onDelete('cascade');
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assignee_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('depended_tc_id')->nullable(true);
            $table->string("title");
            $table->string("requirements");
            $table->boolean("automated")->default(false);
            $table->integer("automated_status")->default(\App\Enums\AutomationStatus::NOT_AUTOMATED);
            $table->string("script_name")->nullable()->comment("Script name of automated test");
            $table->integer("priority")->default(\App\Enums\CasePriority::MEDIUM);
            $table->integer("severity")->default(\App\Enums\CaseSeverity::MEDIUM);
            $table->longText("data")->nullable();
            $table->integer("order")->nullable();
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
        Schema::dropIfExists('test_cases');
    }
}
