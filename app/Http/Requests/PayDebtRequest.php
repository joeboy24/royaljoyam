<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayDebtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'send_id' => 'required|integer|exists:sales,id',
            'send_tot' => 'required|numeric|min:0',
            'amt_paid' => 'required|numeric|min:0.01',
        ];
    }
}
