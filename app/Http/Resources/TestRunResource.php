<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestRunResource extends JsonResource
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
            'test_plan_id' => $this->test_plan_id,
            'project_id' => $this->project_id,
            'version_id' => $this->version_id,
            'version' => $this->version,
            'title' => $this->title,
            'data' => json_decode($this->data),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator_id' => $this->creator_id,
            'creator' => $this->creator,
            'environment' => $this->environment,
            'os' => $this->os,
            'browser' => $this->browser,
            'device' => $this->device,
            'run_parameters' => $this->run_parameters,
            'groups' => $this->groups,
            'priorities' => $this->priorities,
            'is_automation' => $this->is_automation,
            'job_status' => $this->job_status,
            'status' => $this->status,
            'url' => $this->url
        ];
    }
}