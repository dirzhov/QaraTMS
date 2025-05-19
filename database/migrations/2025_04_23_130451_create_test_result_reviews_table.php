<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('test_result_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->tinyInteger("status")->default(5)->comment("(TetResultReviewStatus)actual status of TC review: passed,failed,skipped,on review,not reviewed");
            $table->boolean("is_fixed")->default(false)->comment("fixed or not during the review");
            $table->string("issues")->nullable()->comment("issues created during the analyzing");
            $table->string("comment")->nullable();
            $table->unsignedBigInteger("review_test_result_id")->nullable(false)->comment("failed or passed test result of test rerun");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_result_reviews');
    }
};
