<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScriptNameResource extends JsonResource
{
    /**
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' =>$this->id,
            'script_name' => $this->script_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}