@props([
    'name' => 'customer_phone',
    'value' => null,
    'id' => null,
    'required' => false,
])

@php
    $canonical = \App\Support\PhoneNumber::toE164($value);
    $region = \App\Support\PhoneNumber::regionCode($value) ?? \App\Support\PhoneNumber::DEFAULT_REGION;
@endphp

<div x-data="phoneField(@js($canonical !== '' ? $canonical : $value), @js($region))" class="flex gap-2">
    <select x-model="country"
            aria-label="Код страны"
            class="select select-bordered w-32 shrink-0">
        <template x-for="item in countries" :key="item.iso">
            <option :value="item.iso" x-text="flag(item.iso) + ' +' + item.dial"></option>
        </template>
    </select>

    <input type="tel"
           x-model="local"
           @input="onLocalInput()"
           :placeholder="selected().placeholder"
           inputmode="tel"
           autocomplete="tel-national"
           @if ($id) id="{{ $id }}" @endif
           @if ($required) required @endif
           {{ $attributes->class(['input input-bordered w-full', 'input-error' => $errors->has($name)]) }}>

    <input type="hidden" name="{{ $name }}" :value="e164()">
</div>
