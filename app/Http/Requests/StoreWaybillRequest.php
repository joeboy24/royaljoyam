<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Waybill;

class StoreWaybillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Waybill::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'comp_name' => 'required|string|max:255',
            'comp_add' => 'required|string|max:2000',
            'comp_contact' => 'required|string|max:255',
            'drv_name' => 'required|string|max:255',
            'drv_contact' => 'required|string|max:255',
            'vno' => 'required|string|max:255',
            'bill_no' => 'required|string|max:255|unique:waybills,bill_no',
            'weight' => 'nullable|numeric|min:0',
            'nop' => 'nullable|integer|min:0',
            'tot_qty' => 'nullable|integer|min:0',
            'del_date' => 'nullable|date|max:20',
            'status' => 'required|in:Pending,In Transit,Delivered',
        ];
    }

    public function preparedAttributes(): array
    {
        $validated = $this->validated();

        foreach (['weight', 'nop', 'tot_qty'] as $field) {
            $validated[$field] = filled($validated[$field] ?? null)
                ? (string) $validated[$field]
                : null;
        }

        $validated['del_date'] = filled($validated['del_date'] ?? null)
            ? (string) $validated['del_date']
            : '';

        return $validated;
    }
}
