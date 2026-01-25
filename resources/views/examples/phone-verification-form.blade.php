{{-- 
    Пример формы оформления заказа с верификацией телефона
    Это пример для демонстрации - интегрируйте в вашу существующую форму
--}}

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Оформление заказа с верификацией телефона</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background: #45a049;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .btn-secondary {
            background: #2196F3;
        }
        .btn-secondary:hover {
            background: #0b7dda;
        }
        .hidden {
            display: none;
        }
        .verification-section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 2px solid #e0e0e0;
        }
        .order-section {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #f44336;
            font-weight: bold;
        }
        .info {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1>Оформление заказа</h1>

    {{-- Секция верификации телефона --}}
    <div class="verification-section">
        <h2>1. Верификация номера телефона</h2>
        
        <div class="info">
            📱 Для оформления заказа необходимо подтвердить ваш номер телефона. 
            Мы отправим вам SMS с кодом верификации.
        </div>

        <div class="form-group">
            <label for="customer-phone">Номер телефона *</label>
            <input 
                type="tel" 
                id="customer-phone" 
                name="customer_phone" 
                placeholder="+995555123456"
                required
            >
            <small style="color: #666;">Формат: +995XXXXXXXXX (международный формат)</small>
        </div>

        <button type="button" id="send-code-btn" class="btn-secondary">
            Отправить код верификации
        </button>

        <div class="form-group hidden" id="code-input-group">
            <label for="verification-code">Код верификации</label>
            <input 
                type="text" 
                id="verification-code" 
                maxlength="6" 
                placeholder="123456"
            >
            <small style="color: #666;">Введите 6-значный код из SMS</small>
        </div>

        <button type="button" id="verify-code-btn" class="hidden btn-secondary">
            Проверить код
        </button>

        <div id="verification-status" class="hidden"></div>
    </div>

    {{-- Секция оформления заказа --}}
    <div class="order-section">
        <h2>2. Данные для доставки</h2>

        <form id="order-form">
            <div class="form-group">
                <label for="customer-name">Ваше имя *</label>
                <input 
                    type="text" 
                    id="customer-name" 
                    name="customer_name" 
                    required
                    placeholder="Иван Иванов"
                >
            </div>

            <div class="form-group">
                <label for="customer-email">Email</label>
                <input 
                    type="email" 
                    id="customer-email" 
                    name="customer_email"
                    placeholder="email@example.com"
                >
            </div>

            <div class="form-group">
                <label for="delivery-address">Адрес доставки</label>
                <textarea 
                    id="delivery-address" 
                    name="delivery_address" 
                    rows="3"
                    placeholder="Улица, дом, квартира"
                ></textarea>
            </div>

            <div class="form-group">
                <label for="comment">Комментарий к заказу</label>
                <textarea 
                    id="comment" 
                    name="comment" 
                    rows="3"
                    placeholder="Дополнительная информация"
                ></textarea>
            </div>

            <button type="submit" id="submit-order-btn" disabled>
                Оформить заказ
            </button>
        </form>
    </div>

    <script src="{{ asset('js/phone-verification-example.js') }}"></script>
    
    {{-- Альтернативный встроенный скрипт --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneVerification = new PhoneVerification();
            
            const sendCodeBtn = document.getElementById('send-code-btn');
            const phoneInput = document.getElementById('customer-phone');
            const codeInputGroup = document.getElementById('code-input-group');
            const codeInput = document.getElementById('verification-code');
            const verifyCodeBtn = document.getElementById('verify-code-btn');
            const verificationStatus = document.getElementById('verification-status');
            const orderForm = document.getElementById('order-form');
            const submitOrderBtn = document.getElementById('submit-order-btn');

            // Отправка кода
            sendCodeBtn.addEventListener('click', async function() {
                const phone = phoneInput.value.trim();
                
                if (!phone) {
                    alert('Введите номер телефона');
                    return;
                }

                try {
                    sendCodeBtn.disabled = true;
                    sendCodeBtn.textContent = 'Отправка...';
                    
                    await phoneVerification.sendCode(phone);
                    
                    verificationStatus.textContent = '✓ Код отправлен на ваш номер';
                    verificationStatus.className = 'success';
                    verificationStatus.classList.remove('hidden');
                    
                    codeInputGroup.classList.remove('hidden');
                    verifyCodeBtn.classList.remove('hidden');
                    
                    phoneInput.disabled = true;
                    
                } catch (error) {
                    verificationStatus.textContent = '✗ ' + error.message;
                    verificationStatus.className = 'error';
                    verificationStatus.classList.remove('hidden');
                    
                    sendCodeBtn.disabled = false;
                    sendCodeBtn.textContent = 'Отправить код верификации';
                }
            });

            // Проверка кода
            verifyCodeBtn.addEventListener('click', async function() {
                const code = codeInput.value.trim();
                
                if (!code || code.length !== 6) {
                    alert('Введите 6-значный код');
                    return;
                }

                try {
                    verifyCodeBtn.disabled = true;
                    verifyCodeBtn.textContent = 'Проверка...';
                    
                    await phoneVerification.verifyCode(code);
                    
                    verificationStatus.textContent = '✓ Номер успешно верифицирован!';
                    verificationStatus.className = 'success';
                    
                    codeInput.disabled = true;
                    verifyCodeBtn.disabled = true;
                    
                    submitOrderBtn.disabled = false;
                    
                } catch (error) {
                    verificationStatus.textContent = '✗ ' + error.message;
                    verificationStatus.className = 'error';
                    
                    verifyCodeBtn.disabled = false;
                    verifyCodeBtn.textContent = 'Проверить код';
                }
            });

            // Отправка заказа
            orderForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                if (!phoneVerification.isVerified()) {
                    alert('Необходимо верифицировать номер телефона');
                    return;
                }

                const formData = new FormData(orderForm);
                const orderData = Object.fromEntries(formData.entries());
                
                orderData.verification_request_id = phoneVerification.getRequestId();
                orderData.customer_phone = phoneInput.value;
                
                // Пример товаров (замените на вашу логику корзины)
                orderData.items = [
                    {
                        type: 'dish',
                        id: 1,
                        name: 'Тестовое блюдо',
                        price: 15.50,
                        quantity: 2,
                        calories: 500
                    }
                ];

                try {
                    submitOrderBtn.disabled = true;
                    submitOrderBtn.textContent = 'Оформление...';
                    
                    const response = await fetch('/orders', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': phoneVerification.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(orderData)
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        alert('Заказ успешно оформлен! Номер заказа: ' + data.order.order_number);
                        window.location.href = '/orders/' + data.order.id;
                    } else {
                        throw new Error(data.message || 'Не удалось создать заказ');
                    }
                    
                } catch (error) {
                    alert('Ошибка: ' + error.message);
                    submitOrderBtn.disabled = false;
                    submitOrderBtn.textContent = 'Оформить заказ';
                }
            });
        });
    </script>
</body>
</html>
