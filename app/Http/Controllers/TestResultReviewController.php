<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Enums\TestResultReviewStatus;
use App\Enums\UserPermission;
use App\Http\Resources\TestResultReviewResource;
use App\Models\TestResultReview;
use App\Models\TestRun;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestResultReviewController extends Controller
{
    private User $creator;

    public function useHooks()
    {
        $this->beforeCalling(['store', 'update', 'show'], function ($request, ...$params) {
            if (!auth()->user()->can(UserPermission::view_automation_runs)) {
                return response(null, 403);
            }
            $this->creator = User::findOrFail(Auth::id());
        });
    }

    public function show(Request $request, $test_result_id)
    {
        $review = TestResultReview::where('review_test_result_id', $test_result_id)->first();

        return ApiResponseClass::sendResponse(empty($review) ? null : new TestResultReviewResource($review),'',200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'review_test_result_id' => 'required|integer',
            'reviewer_id' => 'integer|exists:users,id',
            'status' => 'integer|between:1,5',
        ]);

        $review = new TestResultReview();

        $review->review_test_result_id = $request->review_test_result_id;
        $review->reviewer_id = Auth::id();
        if (in_array($request->review_status,
            [TestResultReviewStatus::NOT_REVIEWED->value,
                TestResultReviewStatus::ON_REVIEW->value]))
        {
            $review->status = $request->review_status;
        } else {
            $review->is_fixed = $request->is_fixed;
        }
        $review->comment = $request->review_comment;

        $review->save();

        return ApiResponseClass::sendResponse(new TestResultReviewResource($review),'',200);
    }

    public function update(Request $request)
    {
        $request->validate([
            'review_test_result_id' => 'required|integer',
            'reviewer_id' => 'integer|exists:users,id',
            'status' => 'integer|between:1,5',
        ]);

        $review = TestResultReview::findOrFail($request->id);

        // if (!auth()->user()->can(UserPermission::view_automation_runs)) {
        if ($review->reviewer_id == null) {
            $review->reviewer_id = $request->reviewer_id;
        }

        // if test result already reviewed allow to select a new reviewer
        if (in_array(intval($request->review_status),
                [TestResultReviewStatus::PASSED->value,
                TestResultReviewStatus::FAILED->value,
                TestResultReviewStatus::SKIPPED->value])) {
            $review->reviewer_id = null;
        }

        if (in_array(intval($request->review_status),
            [TestResultReviewStatus::ON_REVIEW->value,
                TestResultReviewStatus::PASSED->value,
                TestResultReviewStatus::FAILED->value,
                TestResultReviewStatus::SKIPPED->value])) {
            $review->is_fixed = (int)(boolean)$request->is_fixed;
        }

        if ($review->status == TestResultReviewStatus::NOT_REVIEWED->value
            && $request->review_status != TestResultReviewStatus::ON_REVIEW->value) {
            // TODO: show an error for transition status
        }

        $review->status = $request->review_status;
        $review->issues = $request->issues;
        $review->comment = $request->review_comment;

        $review->save();

        return ApiResponseClass::sendResponse(new TestResultReviewResource($review),'',200);
    }

}
