<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'pay_mode' => 'required|in:Cash,Cheque,Mobile Money,Post Payment(Debt)',
            'buy_name' => 'required|string|max:255',
            'buy_contact' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'pay_mode.required' => 'Select a mode of payment to proceed.',
            'pay_mode.in' => 'Select a valid mode of payment.',
        ];
    }
}
