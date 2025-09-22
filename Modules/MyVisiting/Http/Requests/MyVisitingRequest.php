<?php

namespace Modules\MyVisiting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MyVisitingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dealer_id'=> ['required'],
            'time'=> ['required'],
            'date' => ['required'],
            'location' => ['required'],
            'photo_of_the_shop'=> ['required', 'file','mimes:jpeg,png,jpg', 'max:20480']
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
