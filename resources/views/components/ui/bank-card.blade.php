@props([
    'bank',
    'iban',
    'ibanFormatted',
    'holder',
    'gradient',
    'scheme' => 'mastercard',
])

<div x-data="{ copied: false }"
     @click="navigator.clipboard.writeText('{{ $iban }}').then(() => { copied = true; $store.cart.showNotification('Номер карты скопирован!', 'success'); setTimeout(() => copied = false, 2000); })"
     class="relative rounded-2xl cursor-pointer select-none overflow-hidden transition-all active:scale-[0.98] flex flex-col justify-between gap-2"
     style="background: {{ $gradient }}; aspect-ratio: 2.1; padding: 4.5% 6%; container-type: inline-size;">

    <!-- Декоративные круги -->
    <div class="absolute -top-10 -right-8 size-28 rounded-full opacity-20"
         style="background: rgba(255,255,255,0.3);"></div>
    <div class="absolute -bottom-8 -left-6 size-24 rounded-full opacity-10"
         style="background: rgba(255,255,255,0.4);"></div>

    <!-- Верхняя строка: лого банка + иконка платёжной системы -->
    <div class="flex items-center justify-between relative z-10">
        <span class="text-white/90 font-bold text-xs tracking-wide">{{ $bank }}</span>
        @if ($scheme === 'visa')
            <span class="text-white/90 font-black text-sm italic tracking-widest">VISA</span>
        @else
            <div class="flex gap-1 items-center">
                <div class="size-5 rounded-full bg-red-500/80"></div>
                <div class="size-5 rounded-full bg-amber-400/80 -ml-2.5"></div>
            </div>
        @endif
    </div>

    <!-- Чип -->
    <div class="relative z-10">
        <div class="w-7 h-5 rounded bg-amber-300/70 flex items-center justify-center">
            <div class="w-5 h-3.5 rounded-sm border border-amber-400/50 grid grid-cols-3 gap-px p-0.5">
                <div class="bg-amber-400/60 rounded-sm"></div>
                <div class="bg-amber-400/60 rounded-sm"></div>
                <div class="bg-amber-400/60 rounded-sm"></div>
                <div class="bg-amber-400/60 rounded-sm"></div>
                <div class="bg-amber-400/60 rounded-sm"></div>
                <div class="bg-amber-400/60 rounded-sm"></div>
            </div>
        </div>
    </div>

    <!-- Номер карты -->
    <div class="relative z-10">
        <p class="text-white font-mono font-bold whitespace-nowrap" style="font-size: clamp(12px, 4.9cqw, 24px); letter-spacing: 0.02em;">
            {{ $ibanFormatted }}
        </p>
    </div>

    <!-- Держатель + индикатор копирования -->
    <div class="flex items-end justify-between relative z-10 gap-2">
        <div class="min-w-0">
            <p class="text-white/60 uppercase mb-0.5" style="font-size: 9px; letter-spacing: 0.05em;">Держатель</p>
            <p class="text-white font-semibold truncate" style="font-size: clamp(10px, 2.4cqw, 13px); letter-spacing: 0.04em;">{{ $holder }}</p>
        </div>
        <!-- Индикатор копирования -->
        <div class="flex items-center gap-1 transition-all shrink-0"
             :class="copied ? 'text-white' : 'text-white/60'"
             style="font-size: 10px;">
            <span x-show="!copied" class="icon-[tabler--copy] size-3"></span>
            <span x-show="copied" class="icon-[tabler--check] size-3 text-emerald-300"></span>
            <span x-text="copied ? 'Скопировано!' : 'Копировать'"></span>
        </div>
    </div>
</div>
