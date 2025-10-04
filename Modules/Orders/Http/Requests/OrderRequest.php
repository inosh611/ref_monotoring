<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'id'=> ['nullable','integer'],
            'order_number'=> ['required','string','max:255'],
            'shop_id'=> ['required','integer'],
            'user_id'=> ['required','integer'],
            'order_status'=> ['required','string','max:255'],
            'payment_status'=> ['required','string','max:255'],
            'total_price' => ['required','numeric'],

            'item_list' => ['required', 'array'],
            'item_list.*.product_id' => ['required','integer'],
            'item_list.*.name' => ['required','string','max:255'],
            'item_list.*.quantity' => ['required','integer','min:1'],
            'item_list.*.price_id' => ['required','integer'],
            'item_list.*.price' => ['required','numeric','min:0'],
            'item_list.*.sub_total' => ['required','numeric','min:0'],
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
    protected function prepareForValidation()
    {
        if ($this->has('item_list') && is_string($this->item_list)) {
            $this->merge([
                'item_list' => json_decode($this->item_list, true),
            ]);
        }
    }
}
