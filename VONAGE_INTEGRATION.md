# Интеграция верификации телефона через Vonage

Это руководство описывает, как настроить и использовать верификацию телефонов через Vonage API v2 при оформлении заказов.

## Установка

### 1. Установка пакета Vonage

Из-за ограничений временной директории PHP, вам нужно установить пакет вручную:

```bash
composer require vonage/vonage-laravel
```

Или, если возникают конфликты зависимостей с Guzzle:

```bash
composer require vonage/client-core
composer require symfony/http-client php-http/message-factory php-http/httplug nyholm/psr7
```

### 2. Настройка конфигурации

Получите ваши API ключи из [Vonage Dashboard](https://dashboard.nexmo.com/):

1. Зарегистрируйтесь или войдите в Vonage Dashboard
2. Найдите ваш API Key и API Secret
3. Добавьте их в файл `.env`:

```env
VONAGE_KEY=your_api_key
VONAGE_SECRET=your_api_secret
```

Если используете альтернативный HTTP клиент:

```env
VONAGE_HTTP_CLIENT="Symfony\\Component\\HttpClient\\HttplugClient"
```

### 3. Применение миграций

Миграции уже были применены. Если нужно применить их заново:

```bash
php artisan migrate
```

Это создаст:
- Таблицу `phone_verifications` для хранения кодов верификации
- Добавит поля `phone_verified` и `phone_verified_at` в таблицу `orders`

## Использование

### API Endpoints

#### 1. Отправка кода верификации

**POST** `/phone/verify/send`

```json
{
  "phone": "+995555123456"
}
```

**Ответ (успех):**
```json
{
  "success": true,
  "message": "Код верификации отправлен на ваш номер",
  "request_id": "request-id-from-vonage"
}
```

#### 2. Проверка кода верификации

**POST** `/phone/verify/check`

```json
{
  "request_id": "request-id-from-vonage",
  "code": "123456"
}
```

**Ответ (успех):**
```json
{
  "success": true,
  "message": "Номер телефона успешно верифицирован",
  "phone": "+995555123456"
}
```

#### 3. Отмена запроса верификации

**POST** `/phone/verify/cancel`

```json
{
  "request_id": "request-id-from-vonage"
}
```

### Создание заказа с верификацией

Теперь при создании заказа необходимо передать `verification_request_id`:

**POST** `/orders`

```json
{
  "customer_name": "Имя Клиента",
  "customer_phone": "+995555123456",
  "customer_email": "email@example.com",
  "delivery_address": "Адрес доставки",
  "comment": "Комментарий",
  "verification_request_id": "request-id-from-vonage",
  "items": [
    {
      "type": "dish",
      "id": 1,
      "name": "Название блюда",
      "price": 15.50,
      "quantity": 2,
      "calories": 500
    }
  ]
}
```

## Процесс верификации на фронтенде

### 1. Запрос кода верификации

```javascript
async function sendVerificationCode(phone) {
  const response = await fetch('/phone/verify/send', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ phone })
  });
  
  const data = await response.json();
  
  if (data.success) {
    return data.request_id;
  } else {
    throw new Error(data.message);
  }
}
```

### 2. Проверка кода

```javascript
async function verifyCode(requestId, code) {
  const response = await fetch('/phone/verify/check', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({ 
      request_id: requestId,
      code: code 
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    return true;
  } else {
    throw new Error(data.message);
  }
}
```

### 3. Создание заказа

```javascript
async function createOrder(orderData, verificationRequestId) {
  const response = await fetch('/orders', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
      ...orderData,
      verification_request_id: verificationRequestId
    })
  });
  
  return await response.json();
}
```

### Полный пример потока

```javascript
try {
  // 1. Отправить код
  const requestId = await sendVerificationCode('+995555123456');
  
  // 2. Показать пользователю поле для ввода кода
  const code = await getUserCodeInput();
  
  // 3. Проверить код
  await verifyCode(requestId, code);
  
  // 4. Создать заказ
  const order = await createOrder({
    customer_name: 'Имя',
    customer_phone: '+995555123456',
    items: [...]
  }, requestId);
  
  console.log('Заказ создан:', order);
} catch (error) {
  console.error('Ошибка:', error.message);
}
```

## Ограничения и безопасность

1. **Попытки проверки**: Максимум 3 попытки проверки кода
2. **Срок действия**: Код действителен 5 минут
3. **Формат номера**: Номер должен быть в международном формате (например, +995555123456)
4. **Длина кода**: 6 цифр

## Тестирование

### Запуск тестов

```bash
# Все тесты верификации
php artisan test --filter=PhoneVerification

# Тесты заказов с верификацией
php artisan test --filter=OrderWithPhoneVerification
```

### Тестовые данные

В тестах используется мокирование HTTP запросов к Vonage API. В продакшене убедитесь, что:

1. У вас есть рабочие API ключи
2. На аккаунте достаточно средств для отправки SMS
3. Номера телефонов в правильном международном формате

## Устранение неполадок

### Ошибка: "Не удалось отправить код верификации"

- Проверьте API ключи в `.env`
- Убедитесь, что на аккаунте Vonage достаточно средств
- Проверьте формат номера телефона

### Ошибка: "Номер телефона не прошёл верификацию"

- Убедитесь, что код был верифицирован перед созданием заказа
- Проверьте, что `verification_request_id` передан корректно
- Проверьте, что номер телефона совпадает с верифицированным

### Ошибка: "Превышено количество попыток"

- Пользователь ввёл неверный код 3 раза
- Нужно запросить новый код верификации

## Дополнительные ресурсы

- [Vonage API Documentation](https://developer.vonage.com/en/api/verify.v2)
- [Vonage Laravel Package](https://github.com/vonage/vonage-laravel)
- [Vonage Dashboard](https://dashboard.nexmo.com/)
