// Класс для работы с верификацией телефона через Vonage
export class PhoneVerification {
    constructor() {
        this.requestId = null;
        this.phone = null;
        this.verified = false;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    }

    /**
     * Отправить код верификации на телефон
     */
    async sendCode(phone, channel = 'sms') {
        try {
            // Нормализуем номер телефона
            phone = this.normalizePhone(phone);
            
            const response = await fetch('/phone/verify/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ phone, channel })
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

    /**
     * Проверить введённый код
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

    /**
     * Отменить текущий запрос верификации
     */
    async cancel() {
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
     * Нормализовать номер телефона
     */
    normalizePhone(phone) {
        // Убираем все нецифровые символы кроме +
        phone = phone.replace(/[^\d+]/g, '');
        
        // Если номер не начинается с +, добавляем +
        if (!phone.startsWith('+')) {
            phone = '+' + phone.replace(/^0+/, '');
        }
        
        return phone;
    }

    /**
     * Получить request_id для передачи при создании заказа
     */
    getRequestId() {
        return this.requestId;
    }

    /**
     * Проверить, был ли телефон верифицирован
     */
    isVerified() {
        return this.verified && this.requestId !== null;
    }

    /**
     * Сбросить состояние
     */
    reset() {
        this.requestId = null;
        this.phone = null;
        this.verified = false;
    }
}
