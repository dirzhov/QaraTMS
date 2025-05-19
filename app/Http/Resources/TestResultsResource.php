<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestResultsResource extends JsonResource
{
    /**
     * Преобразуем ресурс в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request = null): array
    {
        return [
            'id' =>$this->id,
            'test_case_id' => $this->test_case_id,
            'test_run_id' => $this->test_run_id,
            'tc_params' => $this->tc_params,
            'status' => $this->status,
            'failed_step' => $this->failed_step,
            'error_message' => $this->error_message,
            'error_message_hash' => $this->error_message_hash,
            'full_error' => $this->full_error,
            'start_time' => $this->start_time,
            'execution_time' => $this->execution_time,
            'has_screenshot' => $this->screenshot_path != null,
            'script_name_id' => $this->script_name_id,
            'script_name' => $this->script_name,
            'tc_name' => $this->tc_name,
            'priority' => $this->priority,

            'issues' => $this->issues,
            'reviewer_id' => $this->reviewer_id,
            'review_status' => $this->review_status
        ];
    }
}