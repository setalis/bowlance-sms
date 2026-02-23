@extends('layouts.guest')

@section('title', 'Личный кабинет — Вход — ' . config('app.name'))

@section('content')
    <div class="auth-background flex h-auto min-h-screen items-center justify-center overflow-x-hidden bg-cover bg-center bg-no-repeat py-10">
        <div class="relative flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="bg-base-100 shadow-base-300/20 z-1 w-full space-y-6 rounded-xl p-6 shadow-md sm:max-w-md lg:p-8">
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <span class="text-primary">
                            <svg width="32" height="32" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_cabinet)">
                                    <path d="M25.5 0H8.5C3.80558 0 0 3.80558 0 8.5V25.5C0 30.1944 3.80558 34 8.5 34H25.5C30.1944 34 34 30.1944 34 25.5V8.5C34 3.80558 30.1944 0 25.5 0Z" fill="url(#paint0_cabinet)" />
                                </g>
                                <defs>
                                    <linearGradient id="paint0_cabinet" x1="30.28" y1="2.66" x2="4.25" y2="32.41" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="currentColor" />
                                        <stop offset="1" stop-color="currentColor" />
                                    </linearGradient>
                                    <clipPath id="clip0_cabinet"><rect width="34" height="34" fill="white" /></clipPath>
                                </defs>
                            </svg>
                        </span>
                        <h2 class="text-base-content text-xl font-bold">{{ config('app.name') }}</h2>
                    </a>
                </div>
                <div>
                    <h3 class="text-base-content mb-1.5 text-2xl font-semibold">Личный кабинет</h3>
                    <p class="text-base-content/80">Войдите по номеру телефона</p>
                </div>
                
                <div class="space-y-4" x-data="cabinetLogin()">
                    <!-- Шаг 1: Ввод телефона -->
                    <div x-show="step === 1">
                        <form @submit.prevent="sendCode" class="space-y-4">
                            @csrf
                            <div>
                                <label class="label-text" for="userPhone">Телефон *</label>
                                <input type="tel" 
                                       x-model="phone" 
                                       placeholder="+995 5XX XXX XXX" 
                                       class="input w-full" 
                                       id="userPhone" 
                                       required 
                                       autocomplete="tel" 
                                       :disabled="loading" />
                                <div x-show="errors.phone" class="text-error text-sm mt-1" x-text="errors.phone"></div>
                            </div>
                            
                            <button type="submit" 
                                    class="btn btn-lg btn-primary btn-gradient btn-block" 
                                    :disabled="loading || !phone">
                                <span x-show="!loading">Получить код</span>
                                <span x-show="loading" class="loading loading-spinner loading-sm"></span>
                            </button>
                        </form>
                    </div>

                    <!-- Шаг 2: Ввод кода верификации -->
                    <div x-show="step === 2">
                        <div class="mb-4">
                            <button @click="resetForm" class="btn btn-ghost btn-sm gap-2">
                                <span class="icon-[tabler--arrow-left] size-4"></span>
                                Назад
                            </button>
                        </div>

                        <div class="alert mb-4">
                            <span class="icon-[tabler--info-circle] size-5"></span>
                            <div class="text-sm">
                                <p>На номер <strong x-text="phone"></strong> отправлен код подтверждения</p>
                            </div>
                        </div>

                        <form @submit.prevent="verifyCode" class="space-y-4">
                            @csrf
                            <div>
                                <label class="label-text" for="verCode">Код подтверждения *</label>
                                <input type="text" 
                                       x-model="code" 
                                       maxlength="6"
                                       placeholder="••••••" 
                                       class="input w-full text-center text-2xl tracking-widest font-mono" 
                                       id="verCode" 
                                       required
                                       autofocus
                                       @input="code = code.replace(/[^0-9]/g, '')"
                                       :disabled="loading" />
                                <div x-show="errors.code" class="text-error text-sm mt-1" x-text="errors.code"></div>
                            </div>

                            <button type="submit" 
                                    class="btn btn-lg btn-primary btn-gradient btn-block" 
                                    :disabled="loading || code.length !== 6">
                                <span x-show="!loading">Войти</span>
                                <span x-show="loading" class="loading loading-spinner loading-sm"></span>
                            </button>

                            <button type="button" 
                                    @click="resendCode" 
                                    class="btn btn-ghost btn-sm w-full"
                                    :disabled="loading">
                                Отправить код повторно
                            </button>
                        </form>
                    </div>

                    <p class="text-base-content/80 text-center">
                        <a href="{{ route('home') }}" class="link link-animated link-primary font-normal">На главную</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cabinetLogin() {
            return {
                step: 1,
                phone: '',
                code: '',
                requestId: null,
                loading: false,
                errors: {},

                getCsrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.content;
                },

                async sendCode() {
                    this.loading = true;
                    this.errors = {};

                    try {
                        const response = await fetch('/phone/verify/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ 
                                phone: this.normalizePhone(this.phone) 
                            })
                        });

                        if (response.status === 419) {
                            this.errors.phone = 'Сессия истекла. Обновите страницу (F5)';
                            this.loading = false;
                            return;
                        }

                        const data = await response.json();

                        if (data.success) {
                            this.requestId = data.request_id;
                            this.step = 2;
                            
                            // Показываем тестовый код в консоли для разработки
                            if (data.test_mode && data.test_code) {
                                console.log('🔐 Тестовый код верификации:', data.test_code);
                                alert('ТЕСТОВЫЙ РЕЖИМ: Используйте код ' + data.test_code);
                            }
                        } else {
                            this.errors.phone = data.message || 'Не удалось отправить код';
                        }
                    } catch (error) {
                        this.errors.phone = 'Произошла ошибка при отправке кода';
                        console.error(error);
                    } finally {
                        this.loading = false;
                    }
                },

                async verifyCode() {
                    this.loading = true;
                    this.errors = {};

                    try {
                        // Сначала проверяем код через PhoneVerificationController
                        const verifyResponse = await fetch('/phone/verify/check', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                request_id: this.requestId,
                                code: this.code
                            })
                        });

                        if (verifyResponse.status === 419) {
                            this.errors.code = 'Сессия истекла. Обновите страницу (F5)';
                            this.loading = false;
                            return;
                        }

                        const verifyData = await verifyResponse.json();
                        console.log('Результат верификации:', verifyData);

                        if (!verifyData.success) {
                            this.errors.code = verifyData.message || 'Неверный код';
                            this.loading = false;
                            return;
                        }

                        // Теперь отправляем запрос на вход
                        const loginResponse = await fetch('/cabinet/login', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                phone: this.normalizePhone(this.phone),
                                code: this.code,
                                request_id: this.requestId
                            })
                        });

                        if (loginResponse.status === 419) {
                            this.errors.code = 'Сессия истекла. Обновите страницу (F5)';
                            this.loading = false;
                            return;
                        }

                        const loginData = await loginResponse.json();
                        console.log('Результат входа:', loginData);

                        if (loginData.success) {
                            window.location.href = loginData.redirect || '/cabinet';
                        } else {
                            this.errors.code = loginData.message || 'Ошибка входа';
                        }
                    } catch (error) {
                        this.errors.code = 'Произошла ошибка при проверке кода';
                        console.error('Ошибка верификации:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async resendCode() {
                    this.code = '';
                    this.errors = {};
                    await this.sendCode();
                },

                resetForm() {
                    this.step = 1;
                    this.code = '';
                    this.requestId = null;
                    this.errors = {};
                },

                normalizePhone(phone) {
                    let digits = (phone || '').replace(/\D/g, '');
                    if (!digits) return '';
                    if (digits.length === 9 && digits[0] === '5') digits = '995' + digits;
                    else if (digits.length === 10 && digits[0] === '0') digits = '995' + digits.slice(1);
                    return '+' + digits;
                }
            };
        }
    </script>
@endsection
