/**
 * Пример интеграции верификации телефона для оформления заказа
 * 
 * Использование:
 * 1. Подключите этот файл на странице оформления заказа
 * 2. Создайте форму с полями для телефона и кода верификации
 * 3. Используйте функции ниже для обработки верификации
 */

class PhoneVerification {
    constructor() {
        this.requestId = null;
        this.phone = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    }

    /**
     * Отправить код верификации на телефон
     * @param {string} phone - Номер телефона в международном формате
     * @returns {Promise<string>} request_id для последующей проверки
     */
    async sendCode(phone) {
        try {
            // Нормализуем номер телефона
            if (!phone.startsWith('+')) {
                phone = '+' + phone.replace(/^0+/, '');
            }

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
                return data.request_id;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Ошибка отправки кода:', error);
            throw error;
        }
    }

    /**
     * Проверить введённый код
     * @param {string} code - 6-значный код верификации
     * @returns {Promise<boolean>}
     */
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

            return data.success;
        } catch (error) {
            console.error('Ошибка проверки кода:', error);
            throw error;
        }
    }

    /**
     * Отменить текущий запрос верификации
     * @returns {Promise<boolean>}
     */
    async cancelVerification() {
        if (!this.requestId) {
            return true;
        }

        try {
            const response = await fetch('/phone/verify/cancel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    request_id: this.requestId
                })
            });

            const data = await response.json();
            return data.success;
        } catch (error) {
            console.error('Ошибка отмены верификации:', error);
            return false;
        }
    }

    /**
     * Получить request_id для передачи при создании заказа
     * @returns {string|null}
     */
    getRequestId() {
        return this.requestId;
    }

    /**
     * Проверить, был ли телефон верифицирован
     * @returns {boolean}
     */
    isVerified() {
        return this.requestId !== null;
    }

    /**
     * Сбросить состояние
     */
    reset() {
        this.requestId = null;
        this.phone = null;
    }
}

// Пример использования в форме заказа
document.addEventListener('DOMContentLoaded', function() {
    const phoneVerification = new PhoneVerification();
    
    // Кнопка отправки кода
    const sendCodeBtn = document.getElementById('send-code-btn');
    const phoneInput = document.getElementById('customer-phone');
    const codeInput = document.getElementById('verification-code');
    const verifyCodeBtn = document.getElementById('verify-code-btn');
    const orderForm = document.getElementById('order-form');

    // Отправка кода верификации
    if (sendCodeBtn) {
        sendCodeBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const phone = phoneInput.value.trim();
            
            if (!phone) {
                alert('Введите номер телефона');
                return;
            }

            try {
                sendCodeBtn.disabled = true;
                sendCodeBtn.textContent = 'Отправка...';
                
                await phoneVerification.sendCode(phone);
                
                alert('Код верификации отправлен на ваш номер');
                
                // Показать поле для ввода кода
                codeInput.closest('.form-group')?.classList.remove('hidden');
                verifyCodeBtn.classList.remove('hidden');
                
            } catch (error) {
                alert('Ошибка: ' + error.message);
            } finally {
                sendCodeBtn.disabled = false;
                sendCodeBtn.textContent = 'Отправить код';
            }
        });
    }

    // Проверка кода
    if (verifyCodeBtn) {
        verifyCodeBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const code = codeInput.value.trim();
            
            if (!code || code.length !== 6) {
                alert('Введите 6-значный код');
                return;
            }

            try {
                verifyCodeBtn.disabled = true;
                verifyCodeBtn.textContent = 'Проверка...';
                
                const verified = await phoneVerification.verifyCode(code);
                
                if (verified) {
                    alert('Номер успешно верифицирован!');
                    
                    // Отключить поля верификации
                    sendCodeBtn.disabled = true;
                    codeInput.disabled = true;
                    verifyCodeBtn.disabled = true;
                    
                    // Включить кнопку оформления заказа
                    const submitBtn = orderForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }
                }
                
            } catch (error) {
                alert('Ошибка: ' + error.message);
                verifyCodeBtn.disabled = false;
                verifyCodeBtn.textContent = 'Проверить код';
            }
        });
    }

    // Отправка формы заказа
    if (orderForm) {
        orderForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!phoneVerification.isVerified()) {
                alert('Необходимо верифицировать номер телефона');
                return;
            }

            // Собрать данные формы
            const formData = new FormData(orderForm);
            const orderData = Object.fromEntries(formData.entries());
            
            // Добавить request_id верификации
            orderData.verification_request_id = phoneVerification.getRequestId();
            
            // Добавить товары из корзины (пример)
            orderData.items = getCartItems(); // Реализуйте эту функцию

            try {
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
                    // Перенаправить на страницу успеха
                    window.location.href = '/orders/' + data.order.id;
                } else {
                    throw new Error(data.message || 'Не удалось создать заказ');
                }
                
            } catch (error) {
                alert('Ошибка при оформлении заказа: ' + error.message);
            }
        });
    }
});

/**
 * Вспомогательная функция для получения товаров из корзины
 * Реализуйте в соответствии с вашей логикой корзины
 */
function getCartItems() {
    // Пример:
    // return JSON.parse(localStorage.getItem('cart') || '[]');
    
    return [
        {
            type: 'dish',
            id: 1,
            name: 'Пример блюда',
            price: 15.50,
            quantity: 2,
            calories: 500
        }
    ];
}
