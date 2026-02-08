<footer class="bg-base-100 mt-12 border-t border-base-content/10">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            <!-- О компании -->
            <div>
                <div class="mb-4">
                    <a href="{{ route('home') }}" class="inline-block">
                        <img src="{{ asset('storage/images/logo.png') }}" alt="Bowlance" class="h-16">
                    </a>
                </div>
                <p class="text-sm text-base-content/70">
                    Полезная еда быстро и вкусно. Собери свой идеальный боул или выбери из готового меню.
                </p>
            </div>

            <!-- Контакты -->
            <div>
                <h3 class="mb-4 text-lg font-bold">Контакты</h3>
                <div class="space-y-2 text-sm">
                    <a href="tel:+995500700877" class="flex items-center gap-2 hover:text-primary">
                        <span class="icon-[tabler--phone] size-4"></span>
                        +995 500 700 877
                    </a>
                    <a href="mailto:info@bowlance.ge" class="flex items-center gap-2 hover:text-primary">
                        <span class="icon-[tabler--mail] size-4"></span>
                        info@bowlance.ge
                    </a>
                    <div class="flex items-start gap-2">
                        <span class="icon-[tabler--map-pin] size-4 mt-0.5"></span>
                        <span>{{ __('frontend.location') }}</span>
                    </div>
                </div>
            </div>

            <!-- Социальные сети -->
            <div>
                <h3 class="mb-4 text-lg font-bold">Мы в соцсетях</h3>
                <div class="flex gap-3">
                    <a href="https://instagram.com/bowlance.ge" 
                       target="_blank" 
                       class="btn btn-circle btn-ghost" 
                       aria-label="Instagram">
                        <span class="icon-[tabler--brand-instagram] size-6"></span>
                    </a>
                    <a href="https://facebook.com/bowlance" 
                       target="_blank" 
                       class="btn btn-circle btn-ghost" 
                       aria-label="Facebook">
                        <span class="icon-[tabler--brand-facebook] size-6"></span>
                    </a>
                    <a href="https://t.me/bowlance" 
                       target="_blank" 
                       class="btn btn-circle btn-ghost" 
                       aria-label="Telegram">
                        <span class="icon-[tabler--brand-telegram] size-6"></span>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-8 border-t border-base-content/10 pt-6 text-center text-sm text-base-content/60">
            <p>&copy; {{ date('Y') }} Bowlance. Все права защищены.</p>
        </div>
    </div>
</footer>
