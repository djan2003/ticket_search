# Flight Search - Поиск авиабилетов с уведомлениями в Telegram

Приложение на Lumen для автоматического поиска дешевых авиабилетов из Батуми в страны без визовых требований для граждан РФ с отправкой уведомлений в Telegram.

## Возможности

- 🔍 Поиск билетов через Aviasales API
- 🌍 Автоматический поиск по странам без виз для РФ
- 📅 Фильтрация по дням недели (например, пятница туда - воскресенье обратно)
- 📱 Уведомления в Telegram
- ⏰ Автоматический поиск по расписанию (каждые 6 часов)
- 🐳 Полностью готов для запуска в Docker

## Требования

- Docker и Docker Compose
- Telegram бот токен
- Aviasales API токен (партнерский маркер)

## Установка

### 1. Клонирование и настройка окружения

```bash
# Скопируйте .env.example в .env
cp .env.example .env
```

### 2. Создание Telegram бота

1. Откройте Telegram и найдите [@BotFather](https://t.me/BotFather)
2. Отправьте команду `/newbot`
3. Следуйте инструкциям (укажите имя и username бота)
4. Получите токен (формат: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`)
5. Для получения `chat_id`:
   - Отправьте боту любое сообщение
   - Откройте в браузере: `https://api.telegram.org/bot<YOUR_TOKEN>/getUpdates`
   - Найдите `"chat":{"id":123456789}` в ответе

### 3. Получение Aviasales API токена

1. Зарегистрируйтесь на [Travelpayouts](https://www.travelpayouts.com/)
2. Создайте приложение в разделе "API"
3. Получите токен и маркер партнера

### 4. Настройка .env файла

Откройте `.env` и заполните:

```env
# Aviasales API
AVIASALES_API_TOKEN=your_aviasales_token_here
AVIASALES_MARKER=your_marker_here

# Telegram Bot
TELEGRAM_BOT_TOKEN=your_telegram_bot_token_here
TELEGRAM_CHAT_ID=your_telegram_chat_id_here

# Search Configuration
DEFAULT_ORIGIN=BUS          # Батуми
DEFAULT_DEPARTURE_DAY=5      # 5 = Пятница
DEFAULT_RETURN_DAY=0         # 0 = Воскресенье
SEARCH_RANGE_DAYS=90         # Искать на 90 дней вперед
```

### 5. Запуск в Docker

```bash
# Соберите и запустите контейнеры
docker-compose up -d

# Проверьте статус
docker-compose ps

# Выполните миграции
docker-compose exec app php artisan migrate
```

## Использование

### API Endpoints

#### 1. Поиск билетов (с отправкой в Telegram)

```bash
curl -X POST http://localhost/api/search \
  -H "Content-Type: application/json" \
  -d '{
    "origin": "BUS",
    "departure_day": 5,
    "return_day": 0,
    "days_ahead": 90,
    "limit": 10,
    "notify": true
  }'
```

#### 2. Получить список стран без виз

```bash
curl http://localhost/api/countries
```

### Консольная команда

```bash
# Базовый поиск (использует настройки по умолчанию)
docker-compose exec app php artisan flights:search

# С параметрами
docker-compose exec app php artisan flights:search \
  --origin=BUS \
  --departure-day=5 \
  --return-day=0 \
  --days-ahead=90 \
  --limit=10
```

### Автоматический поиск

Автоматический поиск настроен в `app/Console/Kernel.php` и выполняется каждые 6 часов.

Для изменения расписания отредактируйте метод `schedule()`:

```php
// Каждые 6 часов
$schedule->command('flights:search')->everySixHours();

// Или в определенное время
$schedule->command('flights:search')->dailyAt('09:00');
$schedule->command('flights:search')->dailyAt('15:00');
$schedule->command('flights:search')->dailyAt('21:00');
```

## Параметры поиска

| Параметр | Тип | Описание | Пример |
|----------|-----|----------|--------|
| `origin` | string | Код аэропорта отправления (IATA) | `BUS` |
| `departure_day` | integer | День недели вылета (0-6, где 0 = Воскресенье) | `5` (Пятница) |
| `return_day` | integer | День недели возврата (0-6) | `0` (Воскресенье) |
| `days_ahead` | integer | Сколько дней вперед искать | `90` |
| `limit` | integer | Максимальное количество результатов | `10` |
| `notify` | boolean | Отправлять ли в Telegram | `true` |

## Дни недели

- 0 = Воскресенье
- 1 = Понедельник
- 2 = Вторник
- 3 = Среда
- 4 = Четверг
- 5 = Пятница
- 6 = Суббота

## Страны без виз

Приложение ищет билеты в следующие страны (список в `config/countries.php`):

- 🇦🇲 Армения
- 🇧🇾 Беларусь
- 🇰🇿 Казахстан
- 🇰🇬 Киргизия
- 🇲🇩 Молдова
- 🇹🇯 Таджикистан
- 🇺🇿 Узбекистан
- 🇷🇸 Сербия
- 🇹🇷 Турция
- 🇹🇭 Таиланд
- 🇲🇻 Мальдивы
- 🇦🇪 ОАЭ
- 🇮🇱 Израиль
- 🇲🇦 Марокко
- 🇹🇳 Тунис
- 🇨🇺 Куба
- 🇻🇳 Вьетнам
- 🇮🇩 Индонезия

## Логи

```bash
# Просмотр логов приложения
docker-compose logs -f app

# Просмотр логов scheduler
docker-compose logs -f scheduler

# Просмотр логов Laravel
docker-compose exec app tail -f storage/logs/lumen.log
```

## Разработка

### Структура проекта

```
.
├── app/
│   ├── Console/
│   │   ├── Commands/
│   │   │   └── SearchFlightsCommand.php
│   │   └── Kernel.php
│   ├── Http/
│   │   └── Controllers/
│   │       └── FlightSearchController.php
│   └── Services/
│       ├── AviasalesService.php
│       ├── TelegramService.php
│       └── FlightSearchService.php
├── config/
│   ├── airports.php
│   ├── countries.php
│   └── database.php
├── database/
│   └── migrations/
├── docker/
│   ├── php/
│   └── supervisor/
├── routes/
│   └── web.php
├── docker-compose.yml
├── Dockerfile
└── README.md
```

### Добавление новых стран

Отредактируйте `config/countries.php`:

```php
'XX' => [
    'name' => 'Country Name',
    'cities' => ['CITY1', 'CITY2'], // IATA codes
],
```

## Развертывание на сервере

1. Скопируйте проект на сервер
2. Настройте `.env` файл
3. Запустите Docker Compose:

```bash
docker-compose up -d
```

4. Выполните миграции:

```bash
docker-compose exec app php artisan migrate
```

## Troubleshooting

### Telegram не отправляет сообщения

- Проверьте токен бота в `.env`
- Проверьте chat_id
- Убедитесь, что вы отправили хотя бы одно сообщение боту

### Aviasales API не отвечает

- Проверьте токен в `.env`
- Убедитесь, что у вас есть активное API приложение на Travelpayouts
- Проверьте логи: `docker-compose logs app`

### База данных не подключается

- Дождитесь полной инициализации MySQL (может занять минуту)
- Проверьте credentials в `.env`

## Лицензия

MIT

## Поддержка

Для вопросов и предложений создавайте issue в репозитории.
