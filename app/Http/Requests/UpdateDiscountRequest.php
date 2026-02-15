<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'size' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:percent,amount'],
            'scope' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'size.required' => 'Укажите размер скидки.',
            'size.min' => 'Размер скидки не может быть отрицательным.',
            'type.required' => 'Выберите тип скидки (процент или сумма).',
            'type.in' => 'Тип скидки должен быть «процент» или «сумма».',
        ];
    }
}
