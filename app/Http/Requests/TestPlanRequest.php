<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class TestPlanRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(Request $request): array
    {
        $rules = [
            'title' => 'required|string',
            'description' => 'string',
            'project_id' => 'required|integer|gte:1|exists:projects,id',
            'repository_id' => 'required|integer|gte:1|exists:repositories,id',
            'testing_type' => 'required|integer|min:1|max:10',
            'product_version' => 'required|integer|gte:1|exists:product_versions,id',
        ];

        switch ($this->getMethod())
        {
            case 'POST':
                return $rules;
            case 'PUT':
                return [
                        'id' => [
                            'required|integer|exists:test_plans,id',
                        ]
                    ] + $rules;
            case 'GET':
            case 'DELETE':
                return [
                    'id' => 'required|integer|exists:test_plans,id'
                ];
            default:
                return [];
        }
    }

}
