<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'item_id' => 'required|integer|exists:items,id',
            'item_no' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ];
    }
}
