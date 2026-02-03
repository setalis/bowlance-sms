<?php

namespace App\Http\Requests;

use App\Models\PhoneVerification;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'delivery_type' => 'required|in:delivery,pickup',
            'delivery_address' => 'required_if:delivery_type,delivery|nullable|string|max:1000',
            'comment' => 'nullable|string|max:1000',
            'verification_request_id' => 'required|string',
            'confirm_switch_user' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:dish,bowl',
            'items.*.id' => 'required|integer',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.calories' => 'nullable|integer',
            'items.*.proteins' => 'nullable|numeric',
            'items.*.fats' => 'nullable|numeric',
            'items.*.carbs' => 'nullable|numeric',
            'items.*.products' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Необходимо указать имя',
            'customer_phone.required' => 'Необходимо указать номер телефона',
            'customer_email.email' => 'Неверный формат email',
            'delivery_type.required' => 'Необходимо выбрать способ получения',
            'delivery_type.in' => 'Неверный способ получения',
            'delivery_address.required_if' => 'Необходимо указать адрес доставки',
            'delivery_address.max' => 'Адрес доставки слишком длинный',
            'verification_request_id.required' => 'Требуется верификация номера телефона',
            'items.required' => 'Корзина не может быть пустой',
            'items.min' => 'Необходимо добавить хотя бы один товар',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $verification = PhoneVerification::where('request_id', $this->verification_request_id)
                ->where('phone', $this->customer_phone)
                ->first();

            if (! $verification || ! $verification->verified) {
                $validator->errors()->add(
                    'customer_phone',
                    'Номер телефона не прошёл верификацию'
                );
            }

            if ($verification && $verification->isExpired()) {
                $validator->errors()->add(
                    'customer_phone',
                    'Срок действия верификации истёк. Запросите код повторно'
                );
            }
        });
    }
}
