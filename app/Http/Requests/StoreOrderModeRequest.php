<?php

namespace App\Http\Requests;

use App\Enums\OrderMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderModeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'order_mode' => ['required', Rule::enum(OrderMode::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'order_mode.required' => 'Выберите способ заказа.',
            'order_mode.enum' => 'Выбран недопустимый способ заказа.',
        ];
    }
}
