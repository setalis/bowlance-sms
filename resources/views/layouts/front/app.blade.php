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
    function checkoutModal() {
        return {
            open: false,
            loading: false,
            formData: {
                name: '',
                phone: '',
                email: '',
                address: '',
                comment: ''
            },

            async submitOrder() {
                if (this.loading) return;

                this.loading = true;

                try {
                    const result = await this.$store.cart.checkout(this.formData);

                    if (result) {
                        // Успешно оформлен заказ
                        this.open = false;
                        this.resetForm();

                        // Показываем сообщение об успехе
                        alert(`Заказ ${result.order_number} успешно оформлен!\nСумма: ${result.total} ₾\n\nМы свяжемся с вами в ближайшее время.`);
                    }
                } catch (error) {
                    console.error('Ошибка оформления заказа:', error);
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
            }
        }
    }
    </script>

    @stack('scripts')
</body>

</html>
