# Быстрый старт: Интеграция Vonage для верификации телефонов

## Что было реализовано

✅ Интеграция Vonage Verify API v2  
✅ Верификация телефонов при оформлении заказа  
✅ Сервис для работы с Vonage API  
✅ Контроллеры и маршруты для верификации  
✅ Миграции базы данных  
✅ Form Request валидация с проверкой верификации  
✅ Комплексные тесты (Pest)  
✅ Фабрики для тестирования  

## Что нужно сделать вручную

### 1. Исправить проблему с временной директорией PHP

В вашей системе есть проблема с доступом к временной директории PHP. Исправьте это:

**Вариант A: Создать директорию**
```bash
mkdir H:\OSPanel\temp\PHP-8.3\default
```

**Вариант B: Изменить настройку в php.ini**
Откройте `php.ini` и измените:
```ini
sys_temp_dir = "C:\Windows\Temp"
```

### 2. Установить пакет Vonage

После исправления проблемы с temp директорией:

```bash
composer require vonage/vonage-laravel
```

**Или**, если есть конфликты с Guzzle:
```bash
composer require vonage/client-core
composer require symfony/http-client php-http/message-factory php-http/httplug nyholm/psr7
```

И добавьте в `.env`:
```env
VONAGE_HTTP_CLIENT="Symfony\\Component\\HttpClient\\HttplugClient"
```

### 3. Получить API ключи Vonage

1. Зарегистрируйтесь на https://dashboard.nexmo.com/
2. Скопируйте API Key и API Secret
3. Добавьте в `.env`:

```env
VONAGE_KEY=your_api_key_here
VONAGE_SECRET=your_api_secret_here
```

### 4. Запустить тесты

После установки пакета и исправления проблемы с temp:

```bash
# Тесты верификации телефона
php artisan test --filter=PhoneVerification

# Тесты заказов с верификацией
php artisan test --filter=OrderWithPhoneVerification

# Все тесты
php artisan test
```

### 5. Форматирование кода

```bash
vendor/bin/pint --dirty
```

## Структура файлов

### Миграции
- `database/migrations/2026_01_24_171940_add_phone_verification_columns_to_orders_table.php`
- `database/migrations/2026_01_24_171941_create_phone_verifications_table.php`

### Модели
- `app/Models/PhoneVerification.php` - модель для хранения верификаций
- `app/Models/Order.php` - обновлена с полями верификации

### Контроллеры
- `app/Http/Controllers/PhoneVerificationController.php` - отправка и проверка кодов
- `app/Http/Controllers/OrderController.php` - обновлён для работы с верификацией

### Сервисы
- `app/Services/VonageVerifyService.php` - работа с Vonage API

### Form Requests
- `app/Http/Requests/StoreOrderRequest.php` - валидация заказов с верификацией

### Конфигурация
- `config/vonage.php` - настройки Vonage

### Фабрики
- `database/factories/PhoneVerificationFactory.php`

### Тесты
- `tests/Feature/PhoneVerificationTest.php`
- `tests/Feature/OrderWithPhoneVerificationTest.php`

### Маршруты
Добавлено в `routes/web.php`:
- `POST /phone/verify/send` - отправка кода
- `POST /phone/verify/check` - проверка кода
- `POST /phone/verify/cancel` - отмена верификации

## API для фронтенда

### Шаг 1: Отправить код на телефон
```javascript
POST /phone/verify/send
{
  "phone": "+995555123456"
}

// Ответ:
{
  "success": true,
  "request_id": "xxx-xxx-xxx",
  "message": "Код верификации отправлен на ваш номер"
}
```

### Шаг 2: Проверить код
```javascript
POST /phone/verify/check
{
  "request_id": "xxx-xxx-xxx",
  "code": "123456"
}

// Ответ:
{
  "success": true,
  "message": "Номер телефона успешно верифицирован",
  "phone": "+995555123456"
}
```

### Шаг 3: Создать заказ
```javascript
POST /orders
{
  "customer_name": "Имя",
  "customer_phone": "+995555123456",
  "verification_request_id": "xxx-xxx-xxx", // <-- ОБЯЗАТЕЛЬНО!
  "items": [...]
}
```

## Важные моменты

1. **Формат номера**: Обязательно международный формат с `+` (например, `+995555123456`)
2. **Срок действия**: Код действителен 5 минут
3. **Попытки**: Максимум 3 попытки ввода кода
4. **Безопасность**: Номер должен быть верифицирован перед созданием заказа

## Документация

Полная документация: `VONAGE_INTEGRATION.md`

## Следующие шаги

1. ✅ Исправить проблему с temp директорией
2. ✅ Установить `vonage/vonage-laravel`
3. ✅ Получить API ключи и добавить в `.env`
4. ✅ Запустить тесты
5. ✅ Интегрировать на фронтенде (см. примеры в документации)
6. ✅ Протестировать процесс оформления заказа

## Поддержка

Если возникнут вопросы:
- [Vonage API Docs](https://developer.vonage.com/en/api/verify.v2)
- [Vonage Laravel Package](https://github.com/vonage/vonage-laravel)
