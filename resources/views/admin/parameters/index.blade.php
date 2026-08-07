@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-base-content text-2xl font-semibold">Параметры</h2>
            <p class="text-base-content/70">Настройки работы сайта</p>
        </div>

        @session('success')
            <x-ui.alert variant="success">
                {{ $value }}
            </x-ui.alert>
        @endsession

        <div class="bg-base-100 shadow-base-300/20 w-full space-y-6 rounded-xl p-6 shadow-md lg:p-8">
            <form action="{{ route('admin.parameters.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div class="label cursor-pointer justify-start gap-3">
                        <input type="hidden" name="orders_enabled" value="0">
                        <input type="checkbox"
                               name="orders_enabled"
                               class="toggle toggle-primary"
                               value="1"
                               {{ old('orders_enabled', $ordersEnabled) ? 'checked' : '' }} />
                        <span class="label-text font-medium">Приём заказов включён</span>
                    </div>
                    <p class="text-base-content/60 text-sm">
                        В выключенном режиме заказы недоступны: корзина и кнопки заказа не работают, на сайте отображается сообщение о технических работах.
                    </p>
                </div>

                <div class="divider"></div>

                <div>
                    <h3 class="text-base-content text-lg font-semibold mb-1">Интеграции</h3>
                    <p class="text-base-content/60 text-sm mb-4">Управление внешними сервисами при оформлении заказа</p>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <div class="label cursor-pointer justify-start gap-3">
                                <input type="hidden" name="phone_verification_enabled" value="0">
                                <input type="checkbox"
                                       name="phone_verification_enabled"
                                       class="toggle toggle-primary"
                                       value="1"
                                       {{ old('phone_verification_enabled', $phoneVerificationEnabled) ? 'checked' : '' }} />
                                <span class="label-text font-medium">Верификация телефона (SMS и Telegram)</span>
                            </div>
                            <p class="text-base-content/60 text-sm">
                                При выключении на шаге подтверждения доступен только «Звонок менеджера». SMS и Telegram отключаются.
                            </p>
                        </div>

                        <div class="space-y-2">
                            <div class="label cursor-pointer justify-start gap-3">
                                <input type="hidden" name="wolt_delivery_enabled" value="0">
                                <input type="checkbox"
                                       name="wolt_delivery_enabled"
                                       class="toggle toggle-primary"
                                       value="1"
                                       {{ old('wolt_delivery_enabled', $woltDeliveryEnabled) ? 'checked' : '' }} />
                                <span class="label-text font-medium">Доставка Wolt</span>
                            </div>
                            <p class="text-base-content/60 text-sm">
                                При выключении заказы создаются без расчёта и отправки в Wolt Drive. Остальной функционал не меняется.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn btn-lg btn-primary">
                        <span class="icon-[tabler--check] size-5"></span>
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
