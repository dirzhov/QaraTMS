<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestCasesHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_cases_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_case_id')->constrained('test_cases')->onDelete('cascade');
            $table->foreignId('editor_id')->constrained('users')->onDelete('cascade');
            $table->integer("revision")->default(0);
            $table->enum("field", ["requirement", "automated", "automated_status", "script_name", "priority", "severity", "order", "component", "assignee"]);
            $table->string("value_str")->nullable();
            $table->integer("value_int")->nullable();
            $table->timestamp("updated_at");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_cases_history');
    }
}
