<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDishRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'name_ru' => ['required', 'string', 'max:255'],
            'name_ka' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ru' => ['nullable', 'string'],
            'description_ka' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99', 'lt:price'],
            'dish_category_id' => ['required', 'exists:dish_categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
            'weight_volume' => ['nullable', 'string', 'max:255'],
            'calories' => ['nullable', 'integer', 'min:0'],
            'proteins' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'fats' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'carbohydrates' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'fiber' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'sauce_name' => ['nullable', 'string', 'max:255'],
            'sauce_name_ru' => ['nullable', 'string', 'max:255'],
            'sauce_name_ka' => ['nullable', 'string', 'max:255'],
            'sauce_weight_volume' => ['nullable', 'string', 'max:255'],
            'sauce_calories' => ['nullable', 'integer', 'min:0'],
            'sauce_proteins' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'sauce_fats' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'sauce_carbohydrates' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'sauce_fiber' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name_ru.required' => 'Название блюда на русском языке обязательно для заполнения.',
            'name_ru.max' => 'Название не должно превышать 255 символов.',
            'name_ka.max' => 'Название на грузинском языке не должно превышать 255 символов.',
            'price.required' => 'Цена обязательна для заполнения.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть отрицательной.',
            'discount_price.lt' => 'Цена со скидкой должна быть меньше обычной цены.',
            'dish_category_id.required' => 'Категория обязательна для выбора.',
            'dish_category_id.exists' => 'Выбранная категория не существует.',
            'image.image' => 'Файл должен быть изображением.',
            'image.max' => 'Размер изображения не должен превышать 2 МБ.',
        ];
    }
}
