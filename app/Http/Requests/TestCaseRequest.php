<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class TestCaseRequest extends FormRequest
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
            'suite_id' => 'required|integer|gte:1|exists:suites,id',
            'assignee' => 'required|integer|exists:users,id',
            'priority' => 'required|integer|min:1|max:5',
            'severity' => 'required|integer|min:1|max:5',
            'automated' => 'required|integer|min:0|max:1',
            'automated_status' => 'required|integer|min:0|max:3',
            'requirements' => 'required|string',
        ];

        switch ($this->getMethod())
        {
            case 'POST':
                return $rules;
            case 'PUT':
                return [
                        'id' => [
                            'required|integer|exists:test_cases,id',
                            //Rule::unique('games')
                            //->ignore($this->title, 'title') //должен быть уникальным, за исключением себя же
                        ]
                    ] + $rules;
            case 'GET':
            case 'DELETE':
                return [
                    'id' => 'required|integer|exists:test_cases,id'
                ];
            default:
                return [];
        }
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }

}
