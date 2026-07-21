<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConstructorProductRequest extends FormRequest
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
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:constructor_categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
            'weight_volume' => ['nullable', 'string', 'max:255'],
            'calories' => ['nullable', 'integer', 'min:0'],
            'proteins' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'fats' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'carbohydrates' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'fiber' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'sort_order' => ['integer', 'min:0'],
            'poster_bowl_modification_id' => ['nullable', 'integer', 'min:1'],
            'poster_breakfast_modification_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name_ru.required' => 'Название продукта на русском языке обязательно для заполнения.',
            'name_ru.max' => 'Название не должно превышать 255 символов.',
            'name_ka.max' => 'Название на грузинском языке не должно превышать 255 символов.',
            'price.required' => 'Цена обязательна для заполнения.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть отрицательной.',
            'category_ids.required' => 'Выберите хотя бы одну категорию.',
            'category_ids.min' => 'Выберите хотя бы одну категорию.',
            'category_ids.*.exists' => 'Выбранная категория не существует.',
            'image.image' => 'Файл должен быть изображением.',
            'image.max' => 'Размер изображения не должен превышать 2 МБ.',
            'poster_bowl_modification_id.integer' => 'Poster Modification ID (боулы) должен быть числом.',
            'poster_bowl_modification_id.min' => 'Poster Modification ID (боулы) должен быть не меньше 1.',
            'poster_breakfast_modification_id.integer' => 'Poster Modification ID (завтраки) должен быть числом.',
            'poster_breakfast_modification_id.min' => 'Poster Modification ID (завтраки) должен быть не меньше 1.',
        ];
    }
}
