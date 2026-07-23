<?php

namespace App\Http\Requests;

use App\Enums\ConstructorType;
use App\Models\ConstructorCategory;
use Illuminate\Foundation\Http\FormRequest;

class StoreConstructorProductRequest extends FormRequest
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
        $rules = [
            'name' => ['nullable', 'string', 'max:255'],
            'name_ru' => ['required', 'string', 'max:255'],
            'name_ka' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_ru' => ['nullable', 'string'],
            'description_ka' => ['nullable', 'string'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:constructor_categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
            'sort_order' => ['integer', 'min:0'],
            'variants' => ['nullable', 'array'],
        ];

        foreach ($this->selectedConstructorTypes() as $type) {
            $prefix = "variants.{$type->value}";

            $rules[$prefix] = ['required', 'array'];
            $rules["{$prefix}.price"] = ['required', 'numeric', 'min:0', 'max:999999.99'];
            $rules["{$prefix}.weight_volume"] = ['nullable', 'string', 'max:255'];
            $rules["{$prefix}.calories"] = ['nullable', 'integer', 'min:0'];
            $rules["{$prefix}.proteins"] = ['nullable', 'numeric', 'min:0', 'max:999.99'];
            $rules["{$prefix}.fats"] = ['nullable', 'numeric', 'min:0', 'max:999.99'];
            $rules["{$prefix}.carbohydrates"] = ['nullable', 'numeric', 'min:0', 'max:999.99'];
            $rules["{$prefix}.fiber"] = ['nullable', 'numeric', 'min:0', 'max:999.99'];
            $rules["{$prefix}.poster_modification_id"] = ['nullable', 'integer', 'min:1'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = [
            'name_ru.required' => 'Название продукта на русском языке обязательно для заполнения.',
            'name_ru.max' => 'Название не должно превышать 255 символов.',
            'name_ka.max' => 'Название на грузинском языке не должно превышать 255 символов.',
            'category_ids.required' => 'Выберите хотя бы одну категорию.',
            'category_ids.min' => 'Выберите хотя бы одну категорию.',
            'category_ids.*.exists' => 'Выбранная категория не существует.',
            'image.image' => 'Файл должен быть изображением.',
            'image.max' => 'Размер изображения не должно превышать 2 МБ.',
        ];

        foreach (ConstructorType::cases() as $type) {
            $label = $type === ConstructorType::Bowl ? 'боулов' : 'завтраков';
            $prefix = "variants.{$type->value}";

            $messages["{$prefix}.required"] = "Заполните параметры для конструктора {$label}.";
            $messages["{$prefix}.price.required"] = "Цена для конструктора {$label} обязательна.";
            $messages["{$prefix}.price.numeric"] = "Цена для конструктора {$label} должна быть числом.";
            $messages["{$prefix}.price.min"] = "Цена для конструктора {$label} не может быть отрицательной.";
            $messages["{$prefix}.poster_modification_id.integer"] = "Poster Modification ID ({$label}) должен быть числом.";
            $messages["{$prefix}.poster_modification_id.min"] = "Poster Modification ID ({$label}) должен быть не меньше 1.";
        }

        return $messages;
    }

    /**
     * @return list<ConstructorType>
     */
    public function selectedConstructorTypes(): array
    {
        $categoryIds = $this->input('category_ids', []);

        if (! is_array($categoryIds) || $categoryIds === []) {
            return [];
        }

        return ConstructorCategory::query()
            ->whereIn('id', $categoryIds)
            ->pluck('type')
            ->unique()
            ->values()
            ->all();
    }
}
