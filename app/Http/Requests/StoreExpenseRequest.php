<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'desc' => ['nullable', 'string', 'max:500'],
            'expense_cost' => ['required', 'numeric', 'min:0'],
        ];

        if ($this->user()->status === 'Administrator') {
            $rules['branch'] = [
                'required',
                Rule::exists('company_branches', 'id')->where('del', 'no'),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'branch.required' => 'Choose a branch for this expense.',
            'branch.exists' => 'Select a valid branch.',
            'expense_cost.required' => 'Enter the expense amount.',
            'expense_cost.min' => 'Expense amount cannot be negative.',
        ];
    }

    public function branchId(): string
    {
        if ($this->user()->status === 'Administrator') {
            return (string) $this->input('branch');
        }

        return (string) $this->user()->bv;
    }
}
