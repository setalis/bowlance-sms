<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" class="overflow-x-clip overscroll-x-none max-w-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Bowlance' }} | {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>

<body class="overflow-x-clip overscroll-x-none max-w-full" x-data data-orders-enabled="{{ $siteOrdersEnabled ? '1' : '0' }}">
    @php
        $discountLabels = [
            'cartSubtotal' => __('frontend.cart_subtotal'),
            'discountLine' => __('frontend.discount_line'),
            'totalToPay' => __('frontend.total_to_pay'),
            'pickupHint' => __('frontend.discount_pickup_hint'),
            'deliveryApplied' => __('frontend.discount_delivery_applied'),
            'deliveryAddMore' => __('frontend.discount_delivery_add_more'),
            'discountApplied' => __('frontend.discount_applied'),
            'totalPickupPreview' => __('frontend.total_pickup_preview'),
            'totalDeliveryPreview' => __('frontend.total_delivery_preview'),
            'chooseDeliveryOnNextStep' => __('frontend.choose_delivery_on_next_step'),
            'deliveryFeeHint' => __('frontend.delivery_fee_hint'),
            'deliveryFeeFree' => __('frontend.delivery_fee_free'),
            'deliveryFeeAddMore' => __('frontend.delivery_fee_add_more'),
            'badgePickup' => __('frontend.badge_pickup'),
            'badgeDeliveryThreshold' => __('frontend.badge_delivery'),
            'badgeDeliveryFeeShort' => __('frontend.badge_delivery_fee'),
            'badgeDeliveryFreeShort' => __('frontend.badge_delivery_free'),
            'deliveryProviderWolt' => __('frontend.delivery_provider_wolt'),
            'pickupFromStore' => __('frontend.pickup_from_store'),
            'summaryNoDiscount' => __('frontend.summary_no_discount'),
            'promotionsSection' => __('frontend.promotions_section'),
        ];
    @endphp
    <script>
        window.siteOrdersEnabled = @json($siteOrdersEnabled);
        window.phoneVerificationEnabled = @json($phoneVerificationEnabled ?? true);
        window.ordersUnavailableMessage = @json(__('frontend.orders_unavailable'));
        window.discountConfig = {
            pickup: @json($pickupDiscount ? ['size' => (float) $pickupDiscount->size, 'type' => $pickupDiscount->type->value] : null),
            cartTotal: @json($cartTotalDiscounts ?? []),
        };
        window.deliveryConfig = {
            fee: @json((float) config('delivery.fee', 5)),
            freeFrom: @json((float) config('delivery.free_from', 50)),
        };
        window.discountLabels = @json($discountLabels);
    </script>
    @if(!$siteOrdersEnabled)
        <div class="fixed top-0 left-0 right-0 z-[60] min-h-14 bg-warning/95 text-warning-content py-2 px-3 text-center text-sm font-medium shadow-md flex items-center justify-center" role="alert">
            <span>{{ __('frontend.maintenance_banner') }}</span>
        </div>
    @endif
    <!-- Header -->
    @include('layouts.front.header')

    <!-- Offcanvas Drawer (вне header — иначе backdrop перекрывает контент) -->
    <div id="overlay-end-example" class="overlay overlay-open:translate-x-0 drawer drawer-end hidden" role="dialog" tabindex="-1" aria-label="Меню">
        <div class="drawer-header">
            <h3 class="drawer-title">Bowlance</h3>
            <button type="button" class="btn btn-text btn-circle btn-sm absolute end-3 top-3" aria-label="{{ __('frontend.close') }}" data-overlay="#overlay-end-example">
                <span class="icon-[tabler--x] size-5"></span>
            </button>
        </div>
        <div class="drawer-body">
            <div class="flex flex-col gap-6">
                <a href="tel:+995500700877" class="flex items-center justify-start gap-3" aria-label="{{ __('frontend.phone') }}">
                    <span class="icon-[tabler--phone] size-10 text-emerald-600 mr-3"></span>
                    <div class="flex flex-col">
                        <span class="text-xs text-base-content/50">Заказать по телефону:</span>
                        <span class="text-base font-bold">+995 500 700 877</span>
                    </div>
                </a>
                <button type="button" class="flex items-center justify-center gap-3 w-full" aria-label="{{ __('frontend.location') }}">
                    <span class="icon-[tabler--live-view] bg-amber-700 size-10 mr-3"></span>
                    <div class="flex flex-col items-start">
                        <span class="text-xs text-base-content/50 text-start">Пн-Вс 10:00-22:00</span>
                        <span class="text-md font-bold text-start">{{ __('frontend.location') }}</span>
                    </div>
                </button>
                <a href="https://instagram.com/bowlance.ge" target="_blank" rel="noopener" class="flex items-center justify-start gap-3" aria-label="Instagram">
                    <span class="icon-[tabler--brand-instagram] size-10 bg-linear-65 from-pink-400 to-purple-500 mr-3"></span>
                    <div class="flex flex-col items-start">
                        <span class="text-xs text-base-content/50 text-start">Instagram</span>
                        <span class="text-md font-bold text-start">bowlance.ge</span>
                    </div>
                </a>
            </div>
        </div>
        <div class="drawer-footer">
            <button type="button" class="btn btn-soft btn-primary" data-overlay="#overlay-end-example">{{ __('frontend.close') }}</button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-10 max-w-7xl">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.front.footer')

    <!-- Toast уведомления -->
    <div x-data="{
        show: false,
        message: '',
        type: 'success',
        timer: null,
        init() {
            window.addEventListener('cart-notification', (e) => {
                this.message = e.detail.message;
                this.type = e.detail.type || 'success';
                this.show = true;
                clearTimeout(this.timer);
                this.timer = setTimeout(() => { this.show = false; }, 4000);
            });
        }
    }"
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-3"
         class="fixed bottom-6 left-6 z-[110] max-w-sm w-auto">
        <div class="flex items-center gap-3 px-4 py-3.5 rounded-2xl shadow-xl border"
             :class="{
                 'bg-emerald-600 border-emerald-700 text-white': type === 'success',
                 'bg-red-600 border-red-700 text-white': type === 'error',
                 'bg-sky-600 border-sky-700 text-white': type === 'info'
             }">
            <!-- Иконка в круге -->
            <div class="size-8 rounded-full flex items-center justify-center shrink-0"
                 :class="{
                     'bg-emerald-500/40': type === 'success',
                     'bg-red-500/40': type === 'error',
                     'bg-sky-500/40': type === 'info'
                 }">
                <span x-show="type === 'success'" class="icon-[tabler--check] size-4.5"></span>
                <span x-show="type === 'error'" class="icon-[tabler--x] size-4.5"></span>
                <span x-show="type === 'info'" class="icon-[tabler--info-circle] size-4.5"></span>
            </div>
            <span class="text-sm font-medium leading-snug" x-text="message"></span>
            <button @click="show = false" class="ml-1 opacity-60 hover:opacity-100 transition-opacity shrink-0">
                <span class="icon-[tabler--x] size-4"></span>
            </button>
        </div>
    </div>

    <script>
    // Класс для работы с верификацией телефона через Vonage
    class PhoneVerification {
        constructor() {
            this.requestId = null;
            this.phone = null;
            this.verified = false;
            this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        }

        async sendCode(phone) {
            try {
                phone = this.normalizePhone(phone);
                
                const response = await fetch('/phone/verify/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ phone })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Не удалось отправить код');
                }

                if (data.success) {
                    this.requestId = data.request_id;
                    this.phone = phone;
                    return data;
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Ошибка отправки кода:', error);
                throw error;
            }
        }

        async verifyCode(code) {
            if (!this.requestId) {
                throw new Error('Сначала необходимо отправить код');
            }

            try {
                const response = await fetch('/phone/verify/check', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        request_id: this.requestId,
                        code: code
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Неверный код');
                }

                if (data.success) {
                    this.verified = true;
                    return data;
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Ошибка проверки кода:', error);
                throw error;
            }
        }

        normalizePhone(phone) {
            return typeof window.normalizePhone === 'function'
                ? window.normalizePhone(phone)
                : String(phone ?? '').trim();
        }

        getRequestId() {
            return this.requestId;
        }

        isVerified() {
            return this.verified && this.requestId !== null;
        }

        reset() {
            this.requestId = null;
            this.phone = null;
            this.verified = false;
        }
    }

    function checkoutModal() {
        return {
            open: false,
            loading: false,
            step: 1,
            formData: {
                name: '',
                phone: '',
                phoneCountry: window.defaultPhoneCountry || 'GE',
                phoneLocal: '',
                email: '',
                deliveryType: 'delivery',
                deliveryTimeMode: 'asap',
                scheduledTime: '10:00',
                deliveryCity: '',
                deliveryStreet: '',
                deliveryHouse: '',
                entrance: '',
                floor: '',
                apartment: '',
                intercom: '',
                courierComment: '',
                receiverPhone: '',
                leaveAtDoor: false,
                comment: '',
                promoCode: '',
                paymentMethod: 'cash'
            },
            
            phoneVerification: null,
            phoneVerificationEnabled: @json($phoneVerificationEnabled ?? true),
            verificationMethod: '{{ ($phoneVerificationEnabled ?? true) ? (config('vonage.sms_enabled', true) ? 'sms' : 'telegram') : 'callback' }}',
            codeSent: false,
            sendingCode: false,
            verificationCode: '',
            verifyingCode: false,
            phoneVerified: false,
            verificationRequestId: null,
            verificationError: '',
            telegramLink: null,
            telegramStarted: false,
            
            // Адреса
            savedAddresses: [],
            guestAddresses: [],
            selectedAddressId: '',
            selectedGuestAddressIndex: '',
            isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},

            // Wolt: оценка доставки по адресу
            woltDeliveryEnabled: @json($woltDeliveryEnabled ?? false),
            woltEstimate: { loading: false, available: null, fee: null, eta_minutes: null, message: null },
            woltEstimateTimeout: null,
            
            async init() {
                this.phoneVerification = new PhoneVerification();

                // Восстановить состояние Telegram-верификации после возврата из Telegram
                // (Safari на iOS перезагружает вкладку при возврате из другого приложения)
                const saved = this.restoreTelegramSession();
                if (saved) {
                    this.open = true;
                    this.step = 4;
                }
                
                // Загрузить адреса
                if (this.isAuthenticated) {
                    await this.loadSavedAddresses();
                } else {
                    this.loadGuestAddresses();
                }
            },

            phoneCountries: window.phoneCountries || [],

            selectedPhoneCountry() {
                return (typeof window.findPhoneCountry === 'function'
                    ? window.findPhoneCountry(this.formData.phoneCountry)
                    : null) || { dial: '995', placeholder: '555 12 34 56' };
            },

            countryFlag(iso) {
                return typeof window.countryFlag === 'function' ? window.countryFlag(iso) : '';
            },

            onPhoneCountryChange() {
                this.phoneVerified = false;
                this.verificationRequestId = null;
                this.syncPhoneFromLocal();
            },

            onPhoneLocalInput(event) {
                if (event?.target) {
                    this.formData.phoneLocal = event.target.value;
                }

                const typed = String(this.formData.phoneLocal || '').trim();
                if (typed.startsWith('+') || typed.startsWith('00')) {
                    this.setPhoneFromFull(typed);
                }

                this.phoneVerified = false;
                this.verificationRequestId = null;
                this.syncPhoneFromLocal();
            },

            isPhoneComplete() {
                const digits = String(this.formData.phone || '').replace(/\D+/g, '');
                const dialLength = this.selectedPhoneCountry().dial.length;

                return digits.length >= dialLength + 6 && digits.length <= 15;
            },

            syncPhoneFromLocal() {
                this.formData.phone = typeof window.composePhone === 'function'
                    ? window.composePhone(this.formData.phoneCountry, this.formData.phoneLocal)
                    : '';
            },

            setPhoneFromFull(phone) {
                if (typeof window.splitPhone !== 'function') {
                    return;
                }

                const { iso, local } = window.splitPhone(phone, this.formData.phoneCountry);

                this.formData.phoneCountry = iso;
                this.formData.phoneLocal = local;
                this.formData.phone = window.composePhone(iso, local);
            },

            saveTelegramSession() {
                try {
                    this.syncPhoneFromLocal();
                    sessionStorage.setItem('tg_verify', JSON.stringify({
                        requestId: this.verificationRequestId,
                        telegramLink: this.telegramLink,
                        phone: this.formData.phone,
                        method: this.verificationMethod,
                    }));
                } catch (e) {}
            },

            restoreTelegramSession() {
                try {
                    const raw = sessionStorage.getItem('tg_verify');
                    if (!raw) {
                        return false;
                    }
                    const data = JSON.parse(raw);
                    if (!data.requestId) {
                        return false;
                    }
                    this.verificationRequestId = data.requestId;
                    this.phoneVerification.requestId = data.requestId;
                    this.telegramLink = data.telegramLink;
                    this.verificationMethod = data.method || 'telegram';
                    this.setPhoneFromFull(data.phone || '');
                    this.telegramStarted = true;
                    this.codeSent = true;
                    return true;
                } catch (e) {
                    return false;
                }
            },

            clearTelegramSession() {
                try {
                    sessionStorage.removeItem('tg_verify');
                } catch (e) {}
            },

            fetchWoltEstimate() {
                if (!this.woltDeliveryEnabled || this.formData.deliveryType !== 'delivery') {
                    this.woltEstimate = { loading: false, available: null, fee: null, eta_minutes: null, message: null };
                    return;
                }
                const city = (this.formData.deliveryCity || '').trim();
                const street = (this.formData.deliveryStreet || '').trim();
                if (city.length < 2 || street.length < 2) {
                    this.woltEstimate = { loading: false, available: null, fee: null, eta_minutes: null, message: null };
                    return;
                }
                if (this.woltEstimateTimeout) clearTimeout(this.woltEstimateTimeout);
                this.woltEstimateTimeout = setTimeout(async () => {
                    this.woltEstimate.loading = true;
                    this.woltEstimate.available = null;
                    this.woltEstimate.fee = null;
                    this.woltEstimate.eta_minutes = null;
                    this.woltEstimate.message = null;
                    try {
                        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                        const body = {
                            delivery_city: city,
                            delivery_street: street,
                            delivery_house: (this.formData.deliveryHouse || '').trim()
                        };
                        const res = await fetch('/wolt/delivery-estimate', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                            body: JSON.stringify(body)
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.woltEstimate.available = data.available;
                            this.woltEstimate.fee = data.fee || null;
                            this.woltEstimate.eta_minutes = data.eta_minutes ?? null;
                            this.woltEstimate.message = data.message || null;
                        }
                    } catch (e) {
                        this.woltEstimate.message = 'Не удалось проверить адрес';
                    } finally {
                        this.woltEstimate.loading = false;
                    }
                }, 400);
            },
            
            deliveryTimeSlots() {
                const slots = [];
                for (let minutes = 10 * 60; minutes <= 20 * 60; minutes += 30) {
                    const h = String(Math.floor(minutes / 60)).padStart(2, '0');
                    const m = String(minutes % 60).padStart(2, '0');
                    slots.push(`${h}:${m}`);
                }
                return slots;
            },

            goToStep2() {
                this.syncPhoneFromLocal();

                if (!String(this.formData.name || '').trim()) {
                    this.$store.cart.showNotification('Заполните имя', 'error');
                    return;
                }

                if (!this.isPhoneComplete()) {
                    this.$store.cart.showNotification('Проверьте номер телефона и код страны', 'error');
                    return;
                }

                this.step = 2;
            },

            goToStep3() {
                if (this.formData.deliveryType === 'pickup') {
                    this.submitOrder();
                    return;
                }
                if (!this.formData.deliveryCity?.trim() || !this.formData.deliveryStreet?.trim()) {
                    this.$store.cart.showNotification('Укажите город и улицу', 'error');
                    return;
                }
                if (!this.formData.deliveryHouse?.trim()) {
                    this.$store.cart.showNotification('Укажите номер дома', 'error');
                    return;
                }
                this.step = 3;
            },

            goToStep4() {
                if (this.formData.deliveryType === 'pickup') {
                    this.submitOrder();
                    return;
                }
                this.step = 4;
            },
            
            async sendVerificationCode() {
                this.sendingCode = true;
                this.verificationError = '';
                
                try {
                    this.syncPhoneFromLocal();
                    const result = await this.phoneVerification.sendCode(this.formData.phone);
                    this.codeSent = true;
                    this.verificationRequestId = result.request_id;
                    
                    // Сохранить адрес в localStorage для гостей ДО верификации
                    if (!this.isAuthenticated && this.formData.deliveryType === 'delivery' && (this.formData.deliveryCity || this.formData.deliveryStreet)) {
                        this.saveGuestAddress();
                    }
                    
                    // Показываем тестовый код в режиме разработки
                    if (result.test_mode && result.test_code) {
                        this.$store.cart.showNotification(
                            `ТЕСТ: Код отправлен. Используйте: ${result.test_code}`, 
                            'success'
                        );
                        console.log('🔐 Тестовый код верификации:', result.test_code);
                    } else {
                        this.$store.cart.showNotification('Код отправлен на ваш номер', 'success');
                    }
                } catch (error) {
                    this.verificationError = error.message;
                    this.$store.cart.showNotification(error.message, 'error');
                } finally {
                    this.sendingCode = false;
                }
            },
            
            async verifyCode() {
                if (this.verificationCode.length !== 6) {
                    return;
                }
                
                this.verifyingCode = true;
                this.verificationError = '';
                
                try {
                    await this.phoneVerification.verifyCode(this.verificationCode);
                    this.phoneVerified = true;
                    this.clearTelegramSession();
                    this.$store.cart.showNotification('Номер успешно верифицирован!', 'success');
                } catch (error) {
                    this.verificationError = error.message;
                    this.$store.cart.showNotification(error.message, 'error');
                } finally {
                    this.verifyingCode = false;
                }
            },
            
            async resendCode() {
                this.verificationCode = '';
                this.verificationError = '';
                this.codeSent = false;
                this.telegramLink = null;
                this.telegramStarted = false;
                this.phoneVerification.reset();
                if (this.verificationMethod === 'telegram') {
                    await this.startTelegramVerification();
                } else {
                    await this.sendVerificationCode();
                }
            },

            async startTelegramVerification() {
                this.sendingCode = true;
                this.verificationError = '';

                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                    this.syncPhoneFromLocal();
                    const phone = this.phoneVerification.normalizePhone(this.formData.phone);

                    const response = await fetch('/phone/verify/telegram/start', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ phone })
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.message || 'Не удалось создать ссылку Telegram');
                    }

                    this.verificationRequestId = data.request_id;
                    this.phoneVerification.requestId = data.request_id;
                    this.telegramLink = data.telegram_link;
                    this.telegramStarted = true;
                    this.codeSent = true;

                    // Сохранить состояние в sessionStorage — на случай перезагрузки вкладки
                    // (Safari на iOS перезагружает фоновые вкладки при переходе в другое приложение)
                    this.saveTelegramSession();

                    // Сохранить адрес в localStorage для гостей
                    if (!this.isAuthenticated && this.formData.deliveryType === 'delivery' && (this.formData.deliveryCity || this.formData.deliveryStreet)) {
                        this.saveGuestAddress();
                    }

                    window.open(data.telegram_link, '_blank', 'noopener,noreferrer');
                    this.$store.cart.showNotification('Открыт Telegram — нажмите кнопку и введите код', 'success');
                } catch (error) {
                    this.verificationError = error.message;
                    this.$store.cart.showNotification(error.message, 'error');
                } finally {
                    this.sendingCode = false;
                }
            },
            
            orderError: '',
            
            async submitOrder() {
                const isPickup = this.formData.deliveryType === 'pickup';
                const isCallback = this.verificationMethod === 'callback';
                const skipsPhoneVerification = isPickup || isCallback;

                if (!skipsPhoneVerification && !this.phoneVerified) {
                    this.$store.cart.showNotification('Необходимо верифицировать номер телефона', 'error');
                    return;
                }
                
                if (!skipsPhoneVerification && !this.verificationRequestId) {
                    this.$store.cart.showNotification('Ошибка верификации. Попробуйте снова', 'error');
                    return;
                }

                if (!isPickup && !this.formData.deliveryHouse?.trim()) {
                    this.$store.cart.showNotification('Укажите номер дома', 'error');
                    return;
                }
                
                this.loading = true;
                this.orderError = '';
                this.syncPhoneFromLocal();
                
                try {
                    const orderData = {
                        ...this.formData,
                        phone: this.formData.phone,
                        verification_method: isPickup ? null : this.verificationMethod,
                        verification_request_id: skipsPhoneVerification ? null : this.verificationRequestId,
                        confirm_switch_user: this.formData.confirm_switch_user || false,
                        paymentMethod: this.formData.paymentMethod || 'cash',
                        // Явно передаём адрес доставки при отправке (поля могут не попадать в spread при скрытом шаге 1)
                        deliveryCity: (this.formData.deliveryCity || '').trim(),
                        deliveryStreet: (this.formData.deliveryStreet || '').trim(),
                        deliveryHouse: (this.formData.deliveryHouse || '').trim(),
                        deliveryTime: this.formData.deliveryTimeMode === 'scheduled'
                            ? this.formData.scheduledTime
                            : null
                    };
                    
                    const order = await this.$store.cart.checkout(orderData);
                    
                    if (order) {
                        const msg = order.needs_callback
                            ? `Заказ ${order.order_number} оформлен. Менеджер перезвонит вам для подтверждения.`
                            : (order.wolt_tracking_url
                                ? `Заказ ${order.order_number} оформлен. Отслеживание доставки открыто во вкладке.`
                                : (order.delivery_type === 'delivery'
                                    ? `Заказ ${order.order_number} оформлен. Доставка будет уточнена — с вами могут связаться.`
                                    : `Заказ ${order.order_number} успешно оформлен!`));
                        this.$store.cart.showNotification(msg, 'success');
                        if (order.wolt_tracking_url) {
                            window.open(order.wolt_tracking_url, '_blank', 'noopener');
                        }
                        
                        this.resetForm();
                        this.open = false;
                        
                        // Обновить страницу через 2 секунды, чтобы пользователь увидел авторизацию
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                } catch (error) {
                    // Check if it requires user confirmation for switching accounts
                    if (error.requires_confirmation && error.target_user) {
                        const confirmMessage = `Вы авторизованы как другой пользователь.\n\nПереключиться на:\n${error.target_user.name} (${error.target_user.phone})?`;
                        
                        if (confirm(confirmMessage)) {
                            // User confirmed - retry with confirmation flag
                            this.formData.confirm_switch_user = true;
                            await this.submitOrder();
                            return;
                        } else {
                            this.$store.cart.showNotification('Заказ отменён', 'info');
                        }
                    } else {
                        this.orderError = error.message || 'Произошла ошибка при оформлении заказа';
                        this.$store.cart.showNotification(this.orderError, 'error');
                    }
                } finally {
                    this.loading = false;
                }
            },
            
            resetForm() {
                this.formData = {
                    name: '',
                    phone: '',
                    phoneCountry: window.defaultPhoneCountry || 'GE',
                    phoneLocal: '',
                    email: '',
                    deliveryType: 'delivery',
                    deliveryTimeMode: 'asap',
                    scheduledTime: '10:00',
                    deliveryCity: '',
                    deliveryStreet: '',
                    deliveryHouse: '',
                    entrance: '',
                    floor: '',
                    apartment: '',
                    intercom: '',
                    courierComment: '',
                    receiverPhone: '',
                    leaveAtDoor: false,
                    comment: '',
                    promoCode: '',
                    paymentMethod: 'cash'
                };
                this.step = 1;
                this.verificationMethod = this.phoneVerificationEnabled
                    ? '{{ config('vonage.sms_enabled', true) ? 'sms' : 'telegram' }}'
                    : 'callback';
                this.codeSent = false;
                this.verificationCode = '';
                this.phoneVerified = false;
                this.verificationRequestId = null;
                this.verificationError = '';
                this.telegramLink = null;
                this.telegramStarted = false;
                this.clearTelegramSession();
                this.selectedAddressId = '';
                this.selectedGuestAddressIndex = '';
                if (this.phoneVerification) {
                    this.phoneVerification.reset();
                }
            },
            
            closeModal() {
                if (this.loading) {
                    return;
                }
                this.open = false;
                this.resetForm();
            },
            
            handleEsc() {
                if (!this.loading) {
                    this.closeModal();
                }
            },
            
            resolveApplicableDiscount() {
                const { pickup, cartTotal } = window.discountConfig ?? { pickup: null, cartTotal: [] };

                return window.resolveDiscountForType(
                    this.formData.deliveryType,
                    this.subtotal,
                    pickup,
                    cartTotal
                );
            },

            get subtotal() {
                void this.$store.cart.pricingVersion;

                return window.calculateSubtotalFromItems(this.$store.cart.items);
            },

            get pickupHint() {
                return this.$store.cart.pickupHint;
            },

            get deliveryHint() {
                return this.$store.cart.deliveryHint;
            },

            get deliveryFeeHint() {
                return this.$store.cart.deliveryFeeHint;
            },

            get deliveryFee() {
                if (!this.showFinalTotal || this.formData.deliveryType !== 'delivery') {
                    return 0;
                }

                return window.calculateDeliveryFee(this.subtotal, 'delivery');
            },

            get pickupBadge() {
                return this.$store.cart.pickupBadge;
            },

            get deliveryBadge() {
                return this.$store.cart.deliveryBadge;
            },

            get deliveryFeeBadge() {
                return this.$store.cart.deliveryFeeBadge;
            },

            get activeSummaryChips() {
                if (!this.showFinalTotal) {
                    return this.$store.cart.summaryChips;
                }

                const scope = this.formData.deliveryType === 'pickup' ? 'pickup' : 'delivery';

                return window.buildSummaryChips(this.subtotal, window.discountLabels ?? {}, scope);
            },

            get footerTotal() {
                void this.$store.cart.pricingVersion;

                if (this.showFinalTotal) {
                    return this.totalToPay;
                }

                return window.getFooterTotalBeforeSelection(this.subtotal);
            },

            get footerDeliveryFee() {
                if (!this.showFinalTotal) {
                    return this.$store.cart.footerDeliveryFee;
                }

                void this.$store.cart.pricingVersion;

                return this.deliveryFee;
            },

            get footerDiscountAmount() {
                if (!this.showFinalTotal) {
                    return this.$store.cart.footerDiscountAmount;
                }

                return this.discountAmount;
            },

            get deliveryMethodSummary() {
                return this.$store.cart.deliveryMethodSummary;
            },

            get pickupMethodSummary() {
                return this.$store.cart.pickupMethodSummary;
            },

            get footerTotalIsFrom() {
                return !this.showFinalTotal;
            },

            get showFinalTotal() {
                return this.step >= 2;
            },

            get discountAmount() {
                const discount = this.resolveApplicableDiscount();

                if (!discount) {
                    return 0;
                }

                return window.roundMoney(window.calculateDiscountAmount(discount, this.subtotal));
            },

            get totalToPay() {
                if (!this.showFinalTotal) {
                    return this.subtotal;
                }

                const { pickup, cartTotal } = window.discountConfig ?? { pickup: null, cartTotal: [] };

                return window.calculateTotalForType(
                    this.formData.deliveryType,
                    this.subtotal,
                    pickup,
                    cartTotal
                );
            },

            get appliedDiscountMessage() {
                if (!this.showFinalTotal) {
                    return null;
                }

                const discount = this.resolveApplicableDiscount();

                if (!discount) {
                    return null;
                }

                const labels = window.discountLabels ?? {};
                const discountLabel = window.formatDiscountLabel(discount);

                if (this.formData.deliveryType === 'pickup') {
                    return (labels.discountApplied ?? 'Скидка :discount применена').replace(':discount', discountLabel);
                }

                const threshold = parseFloat(discount.min_cart_total).toFixed(2);

                return (labels.discountApplied ?? 'Скидка :discount применена')
                    .replace(':discount', `${discountLabel} (${threshold} ₾)`);
            },
            
            // Методы для работы с адресами
            async loadSavedAddresses() {
                try {
                    const response = await fetch('/user/addresses');
                    const data = await response.json();
                    this.savedAddresses = data.addresses || [];
                    
                    // Если есть дефолтный - выбрать его
                    const defaultAddr = this.savedAddresses.find(a => a.is_default);
                    if (defaultAddr) {
                        this.selectedAddressId = defaultAddr.id;
                        this.loadAddress();
                    }
                } catch (error) {
                    console.error('Ошибка загрузки адресов:', error);
                }
            },
            
            loadAddress() {
                if (this.selectedAddressId) {
                    const addr = this.savedAddresses.find(a => a.id == this.selectedAddressId);
                    if (addr) {
                        this.formData.deliveryCity = addr.delivery_city || '';
                        this.formData.deliveryStreet = addr.delivery_street || '';
                        this.formData.deliveryHouse = addr.delivery_house || '';
                        // Обратная совместимость: старые адреса хранятся одной строкой в addr.address
                        if (!this.formData.deliveryCity && !this.formData.deliveryStreet && addr.address) {
                            this.formData.deliveryCity = 'Batumi';
                            this.formData.deliveryStreet = addr.address;
                        }
                        this.formData.entrance = addr.entrance || '';
                        this.formData.floor = addr.floor || '';
                        this.formData.apartment = addr.apartment || '';
                        this.formData.intercom = addr.intercom || '';
                        this.formData.courierComment = addr.courier_comment || '';
                        this.formData.receiverPhone = addr.receiver_phone || '';
                        this.formData.leaveAtDoor = addr.leave_at_door || false;
                    }
                } else {
                    this.formData.deliveryCity = '';
                    this.formData.deliveryStreet = '';
                    this.formData.deliveryHouse = '';
                    this.formData.entrance = '';
                    this.formData.floor = '';
                    this.formData.apartment = '';
                    this.formData.intercom = '';
                    this.formData.courierComment = '';
                    this.formData.receiverPhone = '';
                    this.formData.leaveAtDoor = false;
                }
            },
            
            loadGuestAddresses() {
                try {
                    const stored = localStorage.getItem('delivery_addresses');
                    if (stored) {
                        this.guestAddresses = JSON.parse(stored);
                        // Автоматически выбрать последний использованный адрес
                        if (this.guestAddresses.length > 0) {
                            this.selectedGuestAddressIndex = 0;
                            this.loadGuestAddress();
                        }
                    }
                } catch (error) {
                    console.error('Ошибка чтения localStorage:', error);
                }
            },
            
            loadGuestAddress() {
                if (this.selectedGuestAddressIndex !== '' && this.guestAddresses[this.selectedGuestAddressIndex]) {
                    const addr = this.guestAddresses[this.selectedGuestAddressIndex];
                    this.formData.deliveryCity = addr.deliveryCity || addr.city || '';
                    this.formData.deliveryStreet = addr.deliveryStreet || addr.street || addr.address || '';
                    this.formData.deliveryHouse = addr.deliveryHouse || addr.house || '';
                    if (!this.formData.deliveryCity && !this.formData.deliveryStreet && addr.address) {
                        this.formData.deliveryStreet = addr.address;
                        this.formData.deliveryCity = 'Batumi';
                    }
                    this.formData.entrance = addr.entrance || '';
                    this.formData.floor = addr.floor || '';
                    this.formData.apartment = addr.apartment || '';
                    this.formData.intercom = addr.intercom || '';
                    this.formData.courierComment = addr.courierComment || '';
                    this.formData.receiverPhone = addr.receiverPhone || '';
                    this.formData.leaveAtDoor = addr.leaveAtDoor || false;
                } else {
                    this.formData.deliveryCity = '';
                    this.formData.deliveryStreet = '';
                    this.formData.deliveryHouse = '';
                    this.formData.entrance = '';
                    this.formData.floor = '';
                    this.formData.apartment = '';
                    this.formData.intercom = '';
                    this.formData.courierComment = '';
                    this.formData.receiverPhone = '';
                    this.formData.leaveAtDoor = false;
                }
            },
            
            saveGuestAddress() {
                try {
                    const key = [this.formData.deliveryCity, this.formData.deliveryStreet, this.formData.deliveryHouse].filter(Boolean).join(', ');
                    const addressObj = {
                        deliveryCity: this.formData.deliveryCity,
                        deliveryStreet: this.formData.deliveryStreet,
                        deliveryHouse: this.formData.deliveryHouse,
                        address: key,
                        entrance: this.formData.entrance,
                        floor: this.formData.floor,
                        apartment: this.formData.apartment,
                        intercom: this.formData.intercom,
                        courierComment: this.formData.courierComment,
                        receiverPhone: this.formData.receiverPhone,
                        leaveAtDoor: this.formData.leaveAtDoor
                    };
                    
                    let addresses = [];
                    const stored = localStorage.getItem('delivery_addresses');
                    if (stored) {
                        addresses = JSON.parse(stored);
                    }
                    
                    addresses = addresses.filter(a => (a.deliveryCity !== addressObj.deliveryCity || a.deliveryStreet !== addressObj.deliveryStreet || a.deliveryHouse !== addressObj.deliveryHouse));
                    addresses.unshift(addressObj);
                    
                    // Хранить максимум 5 последних адресов
                    addresses = addresses.slice(0, 5);
                    
                    localStorage.setItem('delivery_addresses', JSON.stringify(addresses));
                } catch (error) {
                    console.error('Ошибка сохранения в localStorage:', error);
                }
            }
        };
    }
    </script>

    @stack('scripts')
</body>

</html>
