# Laravel Authentication API

> Это RESTful API приложение на базе Laravel с реализацией токен-аутентификации через Laravel Sanctum. 
## 🔧 Требования

* PHP 8.1 или выше
* Composer
* MySQL 5.7 или выше
* Laravel 10.x
* Laravel Sanctum

## 🚀 Установка

### 1. Клонируйте репозиторий:
```bash
git clone https://github.com/webkoth/task-management-system
cd task-management-system
```

### 2. Установите зависимости:
```bash
composer install
```

### 3. Создайте файл конфигурации:
```bash
cp .env.example .env
```

### 4. Настройте подключение к базе данных в `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Сгенерируйте ключ приложения:
```bash
php artisan key:generate
```

### 6. Выполните миграции:
```bash
php artisan migrate
```

### 7. Установите и опубликуйте конфигурацию Sanctum:
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

## ⚙️ Конфигурация Sanctum

Убедитесь, что в файле `app/Http/Kernel.php` добавлен middleware Sanctum:

```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

## 📡 API Endpoints

### Маршруты (не требуют аутентификации)

| Метод | URL | Описание |
|:------|:----|:---------|
| POST | `/api/register` | Регистрация нового пользователя |
| POST | `/api/login` | Аутентификация пользователя |
| POST | `/api/forgot-password` | Запрос на сброс пароля |
| POST | `/api/reset-password` | Сброс пароля |

### Защищенные маршруты (требуют аутентификации)

| Метод | URL | Описание |
|:------|:----|:---------|
| POST | `/api/logout` | Выход из системы |
| GET | `/api/verify-email/{id}/{hash}` | Подтверждение email |
| POST | `/api/email/verification-notification` | Повторная отправка письма для верификации |

## 📋 Использование API

### Аутентификация

> API использует токены Bearer для аутентификации. После успешного входа или регистрации вы получите токен, который нужно включать в заголовок `Authorization` для всех защищенных запросов:

```http
Authorization: Bearer <your-token>
```

### Примеры запросов

#### Регистрация

```http
POST /api/register
Content-Type: application/json

{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

#### Вход

```http
POST /api/login
Content-Type: application/json

{
    "email": "test@example.com",
    "password": "password123"
}
```

#### Успешный ответ:
```json
{
    "token": "1|laravel_sanctum_token...",
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "email_verified_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

## 🔌 Postman Collection

### Импорт коллекции

1. Откройте Postman
2. Нажмите "Import"
3. Выберите файл `Laravel_API.postman_collection.json`

### Настройка окружения

1. Создайте новое окружение в Postman
2. Добавьте следующие переменные:
    * `base_url`: URL вашего API (например, `http://localhost:8000/api`)
    * `auth_token`: оставьте пустым (будет заполняться автоматически после входа)

### Использование коллекции

1. Сначала выполните запрос регистрации или входа
2. Токен автоматически сохранится в переменную окружения
3. Все последующие защищенные запросы будут автоматически использовать этот токен

## 🔒 Безопасность

* Реализована защита от брутфорс-атак через rate limiting
* Все пароли хэшируются с использованием безопасных алгоритмов
* Используется токен-аутентификация через Sanctum
* Реализована верификация email

## 🔌 Swagger

[Документация API](http://localhost:8000/api/documentation)
