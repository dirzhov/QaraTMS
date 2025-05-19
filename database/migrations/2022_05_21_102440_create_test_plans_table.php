<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->integer('repository_id');
            $table->integer('version')->comment("version of tested product");
            $table->integer('type')->default(\App\Enums\TestingType::Regression)->comment("testing type");
            $table->string('title');
            $table->string('description')->nullable();
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
        Schema::dropIfExists('test_plans');
    }
}
