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

<body class="bg-base-200" x-data>
    <!-- Header -->
    @include('layouts.front.header')

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6 max-w-7xl">
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
         class="fixed bottom-4 right-4 z-50 max-w-sm"
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
            formData: {
                name: '',
                phone: '',
                email: '',
                address: '',
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
            
            init() {
                this.phoneVerification = new PhoneVerification();
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
                
                try {
                    const orderData = {
                        ...this.formData,
                        verification_request_id: this.verificationRequestId
                    };
                    
                    const order = await this.$store.cart.checkout(orderData);
                    
                    if (order) {
                        this.$store.cart.showNotification(
                            `Заказ ${order.order_number} успешно оформлен!`,
                            'success'
                        );
                        
                        this.resetForm();
                        this.open = false;
                    }
                } catch (error) {
                    this.$store.cart.showNotification(error.message, 'error');
                } finally {
                    this.loading = false;
                }
            },
            
            resetForm() {
                this.formData = {
                    name: '',
                    phone: '',
                    email: '',
                    address: '',
                    comment: ''
                };
                this.step = 1;
                this.codeSent = false;
                this.verificationCode = '';
                this.phoneVerified = false;
                this.verificationRequestId = null;
                this.verificationError = '';
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
            }
        };
    }
    </script>

    @stack('scripts')
</body>

</html>
