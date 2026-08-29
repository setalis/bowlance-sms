@props([
    'summary',
])

<div {{ $attributes->merge(['class' => 'min-w-0 flex-1 sm:flex-none sm:w-full']) }}>
    <p class="font-semibold text-sm leading-tight">{{ $slot }}</p>
    <p class="flex flex-wrap items-baseline gap-x-1.5 mt-0.5 justify-start sm:justify-center">
        <template x-for="figure in ({{ $summary }}.figures || [])" :key="figure.text + figure.tone">
            <span class="text-base sm:text-lg font-bold leading-none tabular-nums"
                  :class="methodFigureClass(figure.tone)"
                  x-text="figure.text"></span>
        </template>
    </p>
    <template x-for="line in ({{ $summary }}.details || [])" :key="line">
        <p class="text-[11px] text-base-content/50 leading-snug mt-0.5" x-text="line"></p>
    </template>
    <p class="text-[11px] text-base-content/50 leading-snug mt-0.5"
       x-show="{{ $summary }}.caption"
       x-cloak
       x-text="{{ $summary }}.caption"></p>
</div>
