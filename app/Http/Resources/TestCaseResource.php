<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestCaseResource extends JsonResource
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
            'suite_id' => $this->suite_id,
            'assignee_id' => $this->assignee_id,
            'creator_id' => $this->creator_id,
            'title' => $this->title,
            'requirements' => $this->requirements,
            'automated' => $this->automated,
            'automated_status' => $this->automated_status,
            'script_name' => $this->script_name,
            'priority' => $this->priority,
            'severity' => $this->severity,
            'data' => $this->data,
            'order' => $this->order,
            'depended_tc_id' => $this->depended_tc_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'repository_id' => $this->repository_id
        ];
    }
}