<?php

namespace App\Http\Requests;

use App\Enums\DiscountScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'scope' => ['required', Rule::enum(DiscountScope::class)],
            'min_cart_total' => [
                'nullable',
                'required_if:scope,'.DiscountScope::CartTotal->value,
                'numeric',
                'min:0.01',
            ],
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
            'scope.required' => 'Выберите тип применения скидки.',
            'min_cart_total.required_if' => 'Укажите минимальную сумму корзины для скидки по сумме.',
            'min_cart_total.min' => 'Минимальная сумма корзины должна быть больше нуля.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('scope') === DiscountScope::Pickup->value) {
            $this->merge(['min_cart_total' => null]);
        }
    }
}
