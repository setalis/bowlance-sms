<?php

namespace App\Http\Requests;

use App\Enums\DeliveryType;
use App\Models\PhoneVerification;
use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'customer_phone' => ['required', 'string', 'max:20', function (string $attribute, mixed $value, \Closure $fail): void {
                if (! preg_match('/^\+995\d{9}$/', PhoneNumber::toE164(is_string($value) ? $value : null))) {
                    $fail('Укажите корректный грузинский номер телефона (+995 XXX XX XX XX)');
                }
            }],
            'customer_email' => 'nullable|email|max:255',
            'delivery_type' => ['required', Rule::enum(DeliveryType::class)],
            'delivery_time' => ['nullable', 'string', 'regex:/^\d{2}:\d{2}$/', Rule::in($this->allowedDeliveryTimeSlots())],
            'delivery_address' => 'nullable|string|max:1000',
            'delivery_city' => 'required_if:delivery_type,delivery|nullable|string|max:255',
            'delivery_street' => 'required_if:delivery_type,delivery|nullable|string|max:500',
            'delivery_house' => $this->delivery_type === DeliveryType::Delivery->value
                ? 'required|string|max:50'
                : 'nullable|string|max:50',
            'comment' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|in:cash,bank_transfer',
            'verification_method' => 'nullable|in:sms,telegram,callback',
            'verification_request_id' => [
                Rule::requiredIf(fn (): bool => ! $this->skipsPhoneVerification()),
                'nullable',
                'string',
            ],
            'confirm_switch_user' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:dish,bowl,drink,breakfast',
            'items.*.id' => 'required|integer',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.calories' => 'nullable|integer',
            'items.*.proteins' => 'nullable|numeric',
            'items.*.fats' => 'nullable|numeric',
            'items.*.carbs' => 'nullable|numeric',
            'items.*.products' => 'nullable|array',
            'items.*.addons' => 'nullable|array',
            'items.*.addons.*.id' => 'required_with:items.*.addons|integer|exists:dish_addons,id',
            'items.*.addons.*.quantity' => 'required_with:items.*.addons|integer|min:1',
            'items.*.addons.*.name' => 'nullable|string',
            'items.*.addons.*.price' => 'nullable|numeric|min:0',
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
            'delivery_address.max' => 'Адрес доставки слишком длинный',
            'delivery_time.in' => 'Выбранное время доставки недоступно. Выберите время с 10:00 до 20:00.',
            'delivery_city.required_if' => 'Укажите город',
            'delivery_street.required_if' => 'Укажите улицу и дом',
            'delivery_house.required_if' => 'Укажите номер дома',
            'verification_request_id.required_unless' => 'Требуется верификация номера телефона',
            'items.required' => 'Корзина не может быть пустой',
            'items.min' => 'Необходимо добавить хотя бы один товар',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->skipsPhoneVerification()) {
                return;
            }

            $verification = PhoneVerification::where('request_id', $this->verification_request_id)->first();

            if (! $verification) {
                $validator->errors()->add(
                    'customer_phone',
                    'Номер телефона не прошёл верификацию'
                );

                return;
            }

            $normalizedRequest = PhoneNumber::toE164($this->customer_phone);
            $normalizedStored = PhoneNumber::toE164($verification->phone);
            if ($normalizedRequest === '' || $normalizedRequest !== $normalizedStored) {
                $validator->errors()->add(
                    'customer_phone',
                    'Номер телефона не прошёл верификацию'
                );

                return;
            }

            if (! $verification->verified) {
                $validator->errors()->add(
                    'customer_phone',
                    'Номер телефона не прошёл верификацию'
                );

                return;
            }

            if ($verification->isExpired()) {
                $validator->errors()->add(
                    'customer_phone',
                    'Срок действия верификации истёк. Запросите код повторно'
                );
            }
        });
    }

    public function skipsPhoneVerification(): bool
    {
        if ($this->verification_method === 'callback') {
            return true;
        }

        return $this->delivery_type === DeliveryType::Pickup->value
            && blank($this->verification_request_id);
    }

    protected function allowedDeliveryTimeSlots(): array
    {
        $slots = [];
        for ($minutes = 10 * 60; $minutes <= 20 * 60; $minutes += 30) {
            $slots[] = sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
        }

        return $slots;
    }
}
