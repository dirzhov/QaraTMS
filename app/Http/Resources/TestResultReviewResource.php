<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestResultReviewResource extends JsonResource
{
    /**
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->id,
            'rtr_id' => $this->review_test_result_id,
            'reviewer_id' => $this->reviewer_id,
            'status' => $this->status,
            'is_fixed' => $this->is_fixed,
            'issues' => $this->issues,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}