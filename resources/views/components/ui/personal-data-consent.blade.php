@props([
    'id' => 'personal-data-consent',
])

<label {{ $attributes->merge(['class' => 'flex items-start gap-2.5 cursor-pointer']) }} for="{{ $id }}">
    <input type="checkbox"
           id="{{ $id }}"
           class="checkbox checkbox-primary checkbox-sm mt-0.5 shrink-0"
           x-model="formData.personalDataConsent">
    <span class="text-sm leading-snug text-base-content/70">
        {{ __('frontend.personal_data_consent_prefix') }}
        <a href="{{ route('legal.personal-data-consent') }}"
           target="_blank"
           rel="noopener noreferrer"
           class="text-emerald-600 hover:underline"
           @click.stop>
            {{ __('frontend.personal_data_consent_link') }}
        </a>
    </span>
</label>
