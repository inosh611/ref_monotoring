<?php

namespace Modules\Target\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Target extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
         return [
            'id' => 'required|integer|exists:targets,id',
            'employee_reg_no' => 'required|string|exists:users,reg_number',
            'target_value' => 'required|integer|min:1',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function messages()
    {
        return [
            'employee_reg_no.exists' => 'Employee Registration Number does not exist.',
            'target_amount.required' => 'Target amount is required.',
            'target_month.required' => 'Target month is required.',
            'target_year.required' => 'Target year is required.',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
