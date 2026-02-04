<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'label' => 'required|string|max:50',
            'address' => 'required|string|max:500',
            'entrance' => 'nullable|string|max:10',
            'floor' => 'nullable|string|max:10',
            'apartment' => 'nullable|string|max:10',
            'intercom' => 'nullable|string|max:20',
            'courier_comment' => 'nullable|string|max:500',
            'receiver_phone' => 'nullable|string|max:20',
            'leave_at_door' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'label.required' => 'Необходимо указать название адреса',
            'label.max' => 'Название адреса слишком длинное',
            'address.required' => 'Необходимо указать адрес',
            'address.max' => 'Адрес слишком длинный',
            'entrance.max' => 'Номер подъезда слишком длинный',
            'floor.max' => 'Номер этажа слишком длинный',
            'apartment.max' => 'Номер квартиры слишком длинный',
            'intercom.max' => 'Код домофона слишком длинный',
            'courier_comment.max' => 'Комментарий слишком длинный',
            'receiver_phone.max' => 'Номер телефона слишком длинный',
        ];
    }
}
