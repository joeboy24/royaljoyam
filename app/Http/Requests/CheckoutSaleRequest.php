<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'pay_mode' => 'required|in:Cash,Cheque,Mobile Money,Post Payment(Debt)',
            'del_status' => 'required|in:Delivered,Not Delivered',
            'buy_name' => 'required|string|max:255',
            'buy_contact' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'pay_mode.required' => 'Select a mode of payment to proceed.',
            'pay_mode.in' => 'Select a valid mode of payment.',
            'del_status.required' => 'Select a delivery status to proceed.',
            'del_status.in' => 'Select a valid delivery status.',
        ];
    }
}
