<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class TestResultsRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(Request $request): array
    {
        $rules = [
            'test_case_id' => 'required|integer|exists:test_cases,id',
            'test_run_id' => 'required|integer|exists:test_runs,id',
            'status' => 'required|integer|min:1|max:3',
            'failed_step' => 'nullable|integer',
            'error_message' => '',
            'full_error' => '',
            'start_time' => 'required|integer',
            'execution_time' => 'required|integer',
            'screenshot_path' => '',
            'script_name' => ''
        ];

        switch ($this->getMethod())
        {
            case 'POST':
                return $rules;
            case 'PUT':
                return [
                        'id' => [
                            'required',
                            //Rule::unique('games')
                                //->ignore($this->title, 'title') //должен быть уникальным, за исключением себя же
                        ]
                    ] + $rules; // и берем все остальные правила
            // case 'PATCH':
            case 'GET':
            case 'DELETE':
//                return [
//                    'id' => 'required|integer|exists:test_results,id',
//                    'test_case_id' => 'required|integer|exists:test_cases,id',
//                    'test_run_id' => 'required|integer|exists:test_runs,id'
//                ];
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
