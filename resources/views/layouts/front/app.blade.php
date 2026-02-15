<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

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

<body class="" x-data>
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
        init() {
            window.addEventListener('cart-notification', (e) => {
                this.message = e.detail.message;
                this.type = e.detail.type || 'success';
                this.show = true;
                setTimeout(() => { this.show = false; }, 3000);
            });
        }
    }" 
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed bottom-4 right-4 z-[110] max-w-sm"
         style="display: none;">
        <div class="alert" :class="type === 'success' ? 'alert-success' : 'alert-error'">
            <span class="icon-[tabler--check]" x-show="type === 'success'"></span>
            <span class="icon-[tabler--alert-circle]" x-show="type === 'error'"></span>
            <span x-text="message"></span>
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
            phone = phone.replace(/[^\d+]/g, '');
            if (!phone.startsWith('+')) {
                phone = '+' + phone.replace(/^0+/, '');
            }
            return phone;
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
            pickupDiscount: @json($pickupDiscount ? ['size' => (float) $pickupDiscount->size, 'type' => $pickupDiscount->type->value] : null),
            formData: {
                name: '',
                phone: '',
                email: '',
                deliveryType: 'delivery',
                address: '',
                entrance: '',
                floor: '',
                apartment: '',
                intercom: '',
                courierComment: '',
                receiverPhone: '',
                leaveAtDoor: false,
                comment: ''
            },
            
            phoneVerification: null,
            codeSent: false,
            sendingCode: false,
            verificationCode: '',
            verifyingCode: false,
            phoneVerified: false,
            verificationRequestId: null,
            verificationError: '',
            
            // Адреса
            savedAddresses: [],
            guestAddresses: [],
            selectedAddressId: '',
            selectedGuestAddressIndex: '',
            isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
            
            async init() {
                this.phoneVerification = new PhoneVerification();
                
                // Загрузить адреса
                if (this.isAuthenticated) {
                    await this.loadSavedAddresses();
                } else {
                    this.loadGuestAddresses();
                }
            },
            
            goToVerification() {
                if (!this.formData.name || !this.formData.phone) {
                    this.$store.cart.showNotification('Заполните имя и телефон', 'error');
                    return;
                }
                this.step = 2;
            },
            
            async sendVerificationCode() {
                this.sendingCode = true;
                this.verificationError = '';
                
                try {
                    const result = await this.phoneVerification.sendCode(this.formData.phone);
                    this.codeSent = true;
                    this.verificationRequestId = result.request_id;
                    
                    // Сохранить адрес в localStorage для гостей ДО верификации
                    if (!this.isAuthenticated && this.formData.deliveryType === 'delivery' && this.formData.address) {
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
                this.phoneVerification.reset();
                await this.sendVerificationCode();
            },
            
            orderError: '',
            
            async submitOrder() {
                if (!this.phoneVerified) {
                    this.$store.cart.showNotification('Необходимо верифицировать номер телефона', 'error');
                    return;
                }
                
                if (!this.verificationRequestId) {
                    this.$store.cart.showNotification('Ошибка верификации. Попробуйте снова', 'error');
                    return;
                }
                
                this.loading = true;
                this.orderError = '';
                
                try {
                    const orderData = {
                        ...this.formData,
                        verification_request_id: this.verificationRequestId,
                        confirm_switch_user: this.formData.confirm_switch_user || false
                    };
                    
                    const order = await this.$store.cart.checkout(orderData);
                    
                    if (order) {
                        this.$store.cart.showNotification(
                            `Заказ ${order.order_number} успешно оформлен!`,
                            'success'
                        );
                        
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
                    email: '',
                    deliveryType: 'delivery',
                    address: '',
                    entrance: '',
                    floor: '',
                    apartment: '',
                    intercom: '',
                    courierComment: '',
                    receiverPhone: '',
                    leaveAtDoor: false,
                    comment: ''
                };
                this.step = 1;
                this.codeSent = false;
                this.verificationCode = '';
                this.phoneVerified = false;
                this.verificationRequestId = null;
                this.verificationError = '';
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
            
            get totalToPay() {
                const base = this.$store.cart.totalPrice;
                if (this.formData.deliveryType !== 'pickup' || !this.pickupDiscount) {
                    return base;
                }
                const d = this.pickupDiscount;
                let discount = 0;
                if (d.type === 'percent') {
                    discount = base * (d.size / 100);
                } else {
                    discount = Math.min(parseFloat(d.size), base);
                }
                return Math.max(0, Math.round((base - discount) * 100) / 100);
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
                        // Загрузить все поля адреса
                        this.formData.address = addr.address || '';
                        this.formData.entrance = addr.entrance || '';
                        this.formData.floor = addr.floor || '';
                        this.formData.apartment = addr.apartment || '';
                        this.formData.intercom = addr.intercom || '';
                        this.formData.courierComment = addr.courier_comment || '';
                        this.formData.receiverPhone = addr.receiver_phone || '';
                        this.formData.leaveAtDoor = addr.leave_at_door || false;
                    }
                } else {
                    // Очистить, если выбрано "новый адрес"
                    this.formData.address = '';
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
                    this.formData.address = addr.address || '';
                    this.formData.entrance = addr.entrance || '';
                    this.formData.floor = addr.floor || '';
                    this.formData.apartment = addr.apartment || '';
                    this.formData.intercom = addr.intercom || '';
                    this.formData.courierComment = addr.courierComment || '';
                    this.formData.receiverPhone = addr.receiverPhone || '';
                    this.formData.leaveAtDoor = addr.leaveAtDoor || false;
                } else {
                    // Очистить все поля если выбрано "Ввести новый адрес"
                    this.formData.address = '';
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
                    // Создать объект адреса
                    const addressObj = {
                        address: this.formData.address,
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
                    
                    // Удалить дубликаты по адресу и добавить в начало
                    addresses = addresses.filter(a => a.address !== addressObj.address);
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
