<?php

namespace Modules\Dealers\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DealerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('id') ?? $this->input('id');

        return [
            'first_name'=> ['required','string','max:255'],
            'last_name'=> ['required','string','max:255'],
            'nic'=> ['required','string'],
            'contact_number'=> ['regex:/^(?:0\d{9})$/'],['contact_number.regex' => 'This is not a valid Sri Lankan telephone number.'],
            'address'=> ['required','string','max:255'],
            'email'=> ['email', Rule::unique('owners','email')->ignore($id)],
            'owner_position' => ['required', 'string'],
            'business_name' => ['required', 'string'],
            'business_address' => ['required', 'string', 'max:255'],
            'business_tel' => ['required', 'regex:/^(?:0\d{9})$/'], ['business_tel.regex' => 'This is not a valid Sri Lankan telephone number.'],
            'registration_doc'=> ['required','file','mimes:pdf'],
            'sign_application'=> ['required','file','mimes:pdf'],
            'nic_copy' => ['required','file','mimes:jpeg,png,jpg'],
            'photo_of_the_shop'=> ['required', 'file','mimes:jpeg,png,jpg'],
            'lat'=> ['required'],
            'lng'=> ['required'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
