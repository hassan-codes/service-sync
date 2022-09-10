<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => 'bail|required|min:13|max:13',
            'amount' => 'required|numeric',
            'currency' => 'required|exists:'
            'description' => 'required|string|min:8',
            'type' => 'required|string',
            'posted_by' => 'required|exists:\App\Models\User,id'
        ];
    }
}
