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
            'nic'=> ['required','string','max:255'],
            'contact_number'=> ['required','string','max:255'],
            'address'=> ['required','string','max:255'],
            'email'=> ['email', Rule::unique('dealers','email')->ignore($id)],
            'registration_doc'=> ['required','file','mimes:pdf','max:20480'],
            'sign_application'=> ['required','file','mimes:pdf','max:20480'],
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
