@php
    $selectedAddonIds = old('addon_ids', isset($dish) ? $dish->addons->pluck('id')->all() : []);
    $oldPosterIds = old('addon_poster_ids', []);
    $oldPrices = old('addon_prices', []);
@endphp

<div class="space-y-4 border-t border-base-content/10 pt-6">
    <h4 class="text-base-content text-lg font-semibold">Добавки</h4>
    <p class="text-base-content/60 text-sm">Отметьте доступные добавки и укажите Poster Modification ID для этого блюда</p>

    @if($addons->isEmpty())
        <p class="text-base-content/50 text-sm">
            Справочник пуст.
            <a href="{{ route('admin.dish-addons.create') }}" class="link link-primary">Создать добавку</a>
        </p>
    @else
        <div class="space-y-3">
            @foreach($addons as $addon)
                @php
                    $attached = isset($dish) ? $dish->addons->firstWhere('id', $addon->id) : null;
                    $isChecked = in_array($addon->id, $selectedAddonIds, false);
                    $posterValue = $oldPosterIds[$addon->id] ?? $attached?->pivot?->poster_modification_id;
                    $priceValue = $oldPrices[$addon->id] ?? $attached?->pivot?->price;
                @endphp
                <div class="rounded-lg bg-base-200/50 p-4 space-y-3">
                    <label class="inline-flex cursor-pointer items-center gap-2">
                        <input type="checkbox"
                               name="addon_ids[]"
                               value="{{ $addon->id }}"
                               class="checkbox"
                               @checked($isChecked) />
                        <span class="label-text font-medium">{{ $addon->name }}</span>
                        <span class="text-base-content/50 text-sm">({{ number_format($addon->price, 2) }} ₾)</span>
                    </label>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label class="label-text" for="addon_poster_{{ $addon->id }}">Poster Modification ID</label>
                            <input type="number"
                                   name="addon_poster_ids[{{ $addon->id }}]"
                                   id="addon_poster_{{ $addon->id }}"
                                   class="input"
                                   value="{{ $posterValue }}"
                                   min="1"
                                   placeholder="ID модификатора для этого блюда" />
                        </div>
                        <div>
                            <label class="label-text" for="addon_price_{{ $addon->id }}">Цена override</label>
                            <input type="number"
                                   name="addon_prices[{{ $addon->id }}]"
                                   id="addon_price_{{ $addon->id }}"
                                   class="input"
                                   value="{{ $priceValue }}"
                                   step="0.01"
                                   min="0"
                                   placeholder="Пусто = цена из справочника" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
