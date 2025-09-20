<?php

namespace Modules\Employee\Http\Requests;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        $id = $this->route('id') ?? $this->input('id');

        return [
            'id'=> ['nullable','integer'],
            'first_name'=> ['required','string','max:255'],
            'last_name'=> ['required','string','max:255'],
            'nic'=> ['required','string','max:255'],
            'contact_number'=> ['required','string','max:255'],
            'address'=> ['required','string','max:255'],
            'email' => ['required','email', Rule::unique('users', 'email')->ignore($id)],
            'position' => ['required','string'],
            'roll_name' => ['required','string', Rule::exists('roles','name')],
        ];
    }


    public function messages():array{
        return [
            'first_name.required'=> 'First name is required.',
            'last_name.required'=> 'Last name is required.',
            'nic.required'=> 'NIC is required.',
            'contact_number.required'=> 'Contact number is required.',
            'address.required'=> 'Address is required.',
            'email.required' => 'Email is required.',
            'email.email'=> 'Please enter a valid email address.',
            'email.unique' => 'This email is already taken.',
            'position.required' => 'Position is required.',
            'role_name.required' => 'Role is required.',
            'role_name.exists'=> 'Selected role is invalid.',
        ];
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
   
}
