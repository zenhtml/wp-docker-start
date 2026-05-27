# WordPress Docker Dev Environment

Локальная среда разработки WordPress на Docker с wp-cli, отладкой и живым редактированием файлов.

## Требования

- [Docker](https://www.docker.com/) + Docker Compose

## Быстрый старт

```powershell
# 1. Скопировать конфигурацию
copy .env.example .env

# 2. Отредактировать .env (пароли, порты, название проекта)
notepad .env

# 3. Запустить
docker compose up -d

# 4. Установить WordPress (первый запуск)
.\scripts\wp-cli.ps1 core install `
    --url=http://localhost:8080 `
    --title="My WordPress Site" `
    --admin_user=admin `
    --admin_password=admin123 `
    --admin_email=admin@example.com
```

После запуска открыть http://localhost:8080

## Структура проекта

```
docker-web/
├── .env                         # Конфигурация (не коммитится)
├── .env.example                 # Шаблон конфигурации
├── .gitignore
├── docker-compose.yml
├── php/
│   ├── Dockerfile               # PHP-Apache + wp-cli
│   └── php.ini                  # Настройки PHP
├── wordpress/
│   ├── wp-config.php            # Конфиг WP (читает из .env)
│   └── wp-content/
│       ├── debug.log            # Лог отладки (на хосте)
│       ├── plugins/custom-plugins/   # Кастомные плагины
│       ├── themes/custom-themes/     # Кастомные темы
│       └── mu-plugins/               # Must-use плагины
├── mysql/                       # SQL-дампы для инициализации БД
└── scripts/
    └── wp-cli.ps1               # Шорткат для wp-cli
```

## Сервисы

| Сервис     | Описание                        | URL/Порт                    |
| ---------- | ------------------------------- | --------------------------- |
| wordpress  | PHP-Apache + WordPress          | http://localhost:8080       |
| mysql      | MariaDB 11                      | localhost:3306              |
| wpcli      | WP-CLI (по требованию)          | —                           |
| phpmyadmin | Управление БД (опционально)     | http://localhost:8081       |
| mailhog    | Перехват email (опционально)    | http://localhost:8025       |

## Команды

### Запуск и остановка

```powershell
# Основные сервисы (wordpress + mysql)
docker compose up -d

# С phpMyAdmin
docker compose --profile pma up -d

# С MailHog
docker compose --profile mail up -d

# Всё вместе
docker compose --profile pma --profile mail up -d

# Остановка
docker compose down

# Остановка с удалением данных БД
docker compose down -v
```

### WP-CLI

```powershell
# Список плагинов
.\scripts\wp-cli.ps1 plugin list

# Установить плагин
.\scripts\wp-cli.ps1 plugin install woocommerce --activate

# Список тем
.\scripts\wp-cli.ps1 theme list

# Пересоздать .htaccess
.\scripts\wp-cli.ps1 rewrite structure '/%postname%/'

# Экспорт базы
.\scripts\wp-cli.ps1 db export - > backup.sql

# Статус ядра
.\scripts\wp-cli.ps1 core verify-checksums
```

## Конфигурация

Все настройки задаются в `.env` (скопируйте из `.env.example`):

| Переменная            | Описание                    | По умолчанию          |
| --------------------- | --------------------------- | --------------------- |
| `PROJECT_NAME`        | Название проекта            | `mywordpress`         |
| `DB_NAME`             | Имя базы данных             | `mywordpress_db`      |
| `DB_USER`             | Пользователь БД             | `mywordpress_user`    |
| `DB_PASSWORD`         | Пароль БД                   | `change_this_password`|
| `DB_ROOT_PASSWORD`    | Пароль root БД              | `change_this_root_password` |
| `WP_TABLE_PREFIX`     | Префикс таблиц WP           | `wp_`                 |
| `WP_DEBUG`            | Включить отладку WP         | `true`                |
| `WP_DEBUG_LOG`        | Писать лог в debug.log      | `true`                |
| `WP_DEBUG_DISPLAY`    | Показывать ошибки на экране | `false`               |
| `PHP_MEMORY_LIMIT`    | Лимит памяти PHP            | `256M`                |
| `PHP_UPLOAD_MAX_FILESIZE` | Макс. размер загрузки   | `64M`                 |
| `WP_PORT`             | Порт WordPress              | `8080`                |
| `PMA_PORT`            | Порт phpMyAdmin             | `8081`                |
| `MAIL_PORT`           | Порт MailHog                | `8025`                |

## Разработка

### Кастомные плагины

Создайте папку с плагином внутри `wordpress/wp-content/plugins/custom-plugins/`:

```
wordpress/wp-content/plugins/custom-plugins/
└── my-plugin/
    └── my-plugin.php
```

WordPress автоматически обнаружит плагин — он появится в админке.

### Кастомные темы

Аналогично — внутри `wordpress/wp-content/themes/custom-themes/`:

```
wordpress/wp-content/themes/custom-themes/
└── my-theme/
    ├── style.css
    ├── index.php
    └── functions.php
```

### Must-use плагины

Файлы в `wordpress/wp-content/mu-plugins/` активируются автоматически (без возможности отключения из админки).

### Отладка

При `WP_DEBUG_LOG=true` WordPress пишет лог в `wordpress/wp-content/debug.log` — файл доступен прямо на хосте, заходить в контейнер не нужно.

```powershell
# Смотреть лог в реальном времени
Get-Content wordpress\wp-content\debug.log -Wait -Tail 50
```

## FAQ

**Q: Как импортировать существующую базу?**

Положите SQL-дамп в `mysql/init.sql` — он выполнится при первом запуске контейнера MariaDB. Или используйте wp-cli: `.\scripts\wp-cli.ps1 db import backup.sql`

**Q: Как сбросить всё и начать заново?**

```powershell
docker compose down -v
docker compose up -d
```

Флаг `-v` удалит volumes с данными БД и файлами ядра WP.
