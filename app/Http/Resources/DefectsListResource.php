<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DefectsListResource extends JsonResource
{
    /**
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->tc_id,
            'prefix' =>$this->prefix,
            'priority' =>$this->tc_priority,
            'is_fixed' =>$this->tc_is_fixed,
            'issues' =>$this->issues,

        ];
    }
}