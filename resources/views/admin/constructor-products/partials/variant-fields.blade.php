@php
    use App\Enums\ConstructorType;

    /** @var \App\Models\ConstructorProduct|null $product */
    $product = $product ?? null;
@endphp

@foreach(ConstructorType::cases() as $constructorType)
    @php
        $typeKey = $constructorType->value;
        $variant = $product?->variantFor($constructorType);
        $label = $constructorType === ConstructorType::Bowl ? 'боулов' : 'завтраков';
        $priceErrorKey = "variants.{$typeKey}.price";
        $weightErrorKey = "variants.{$typeKey}.weight_volume";
        $caloriesErrorKey = "variants.{$typeKey}.calories";
        $proteinsErrorKey = "variants.{$typeKey}.proteins";
        $fatsErrorKey = "variants.{$typeKey}.fats";
        $carbsErrorKey = "variants.{$typeKey}.carbohydrates";
        $fiberErrorKey = "variants.{$typeKey}.fiber";
        $posterErrorKey = "variants.{$typeKey}.poster_modification_id";
    @endphp
    <div class="space-y-4 border-t border-base-content/10 pt-6"
         data-variant-type="{{ $typeKey }}">
        <h4 class="text-base-content text-lg font-semibold">Параметры для конструктора {{ $label }}</h4>
        <p class="text-base-content/60 text-xs">Заполняется, если выбрана хотя бы одна категория этого конструктора</p>

        <div>
            <label class="label-text" for="variants_{{ $typeKey }}_price">Цена*</label>
            <input type="number"
                   name="variants[{{ $typeKey }}][price]"
                   placeholder="0.00"
                   class="input @error($priceErrorKey) input-error @enderror"
                   id="variants_{{ $typeKey }}_price"
                   value="{{ old($priceErrorKey, $variant?->price) }}"
                   step="0.01"
                   min="0" />
            @error($priceErrorKey)
                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="label-text" for="variants_{{ $typeKey }}_weight_volume">Вес/Объем</label>
                <input type="text"
                       name="variants[{{ $typeKey }}][weight_volume]"
                       placeholder="Например: 100 г"
                       class="input @error($weightErrorKey) input-error @enderror"
                       id="variants_{{ $typeKey }}_weight_volume"
                       value="{{ old($weightErrorKey, $variant?->weight_volume) }}" />
                @error($weightErrorKey)
                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="label-text" for="variants_{{ $typeKey }}_calories">Калории (ккал)</label>
                <input type="number"
                       name="variants[{{ $typeKey }}][calories]"
                       placeholder="0"
                       class="input @error($caloriesErrorKey) input-error @enderror"
                       id="variants_{{ $typeKey }}_calories"
                       value="{{ old($caloriesErrorKey, $variant?->calories) }}"
                       min="0" />
                @error($caloriesErrorKey)
                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <div>
                <label class="label-text" for="variants_{{ $typeKey }}_proteins">Белки (г)</label>
                <input type="number"
                       name="variants[{{ $typeKey }}][proteins]"
                       placeholder="0.00"
                       class="input @error($proteinsErrorKey) input-error @enderror"
                       id="variants_{{ $typeKey }}_proteins"
                       value="{{ old($proteinsErrorKey, $variant?->proteins) }}"
                       step="0.01"
                       min="0" />
                @error($proteinsErrorKey)
                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="label-text" for="variants_{{ $typeKey }}_fats">Жиры (г)</label>
                <input type="number"
                       name="variants[{{ $typeKey }}][fats]"
                       placeholder="0.00"
                       class="input @error($fatsErrorKey) input-error @enderror"
                       id="variants_{{ $typeKey }}_fats"
                       value="{{ old($fatsErrorKey, $variant?->fats) }}"
                       step="0.01"
                       min="0" />
                @error($fatsErrorKey)
                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="label-text" for="variants_{{ $typeKey }}_carbohydrates">Углеводы (г)</label>
                <input type="number"
                       name="variants[{{ $typeKey }}][carbohydrates]"
                       placeholder="0.00"
                       class="input @error($carbsErrorKey) input-error @enderror"
                       id="variants_{{ $typeKey }}_carbohydrates"
                       value="{{ old($carbsErrorKey, $variant?->carbohydrates) }}"
                       step="0.01"
                       min="0" />
                @error($carbsErrorKey)
                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="label-text" for="variants_{{ $typeKey }}_fiber">Клетчатка (г)</label>
                <input type="number"
                       name="variants[{{ $typeKey }}][fiber]"
                       placeholder="0.00"
                       class="input @error($fiberErrorKey) input-error @enderror"
                       id="variants_{{ $typeKey }}_fiber"
                       value="{{ old($fiberErrorKey, $variant?->fiber) }}"
                       step="0.01"
                       min="0" />
                @error($fiberErrorKey)
                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div>
            <label class="label-text" for="variants_{{ $typeKey }}_poster_modification_id">Poster Modification ID</label>
            <input type="number"
                   name="variants[{{ $typeKey }}][poster_modification_id]"
                   placeholder="ID модификатора Poster"
                   class="input @error($posterErrorKey) input-error @enderror"
                   id="variants_{{ $typeKey }}_poster_modification_id"
                   value="{{ old($posterErrorKey, $variant?->poster_modification_id) }}"
                   min="1" />
            @error($posterErrorKey)
                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>
    </div>
@endforeach
