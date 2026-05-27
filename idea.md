# WordPress Docker Dev Environment

Универсальная сборка WordPress на Docker для локальной разработки.

---

## Структура проекта

```
docker-web/
├── .env                        # Конфигурация проекта (не коммитится)
├── .env.example                # Шаблон конфигурации
├── .gitignore
├── docker-compose.yml          # Основной compose-файл
├── php/
│   ├── Dockerfile              # Кастомный образ PHP-Apache + wp-cli
│   └── php.ini                 # Настройки PHP
├── wordpress/
│   ├── wp-config.php           # wp-config (читает переменные из .env)
│   └── wp-content/
│       ├── debug.log           # Лог отладки WP (доступен на хосте)
│       ├── plugins/
│       │   └── custom-plugins/ # Кастомные плагины
│       ├── themes/
│       │   └── custom-themes/  # Кастомные темы
│       └── mu-plugins/         # Must-use плагины (автовключение)
├── mysql/
│   └── init.sql                # (опционально) SQL-дамп для инициализации БД
└── scripts/
    └── wp-cli.sh               # Шорткат для запуска wp-cli
```

## Сервисы

| Сервис       | Описание                              | Порт    |
| ------------ | ------------------------------------- | ------- |
| wordpress    | PHP-Apache + WordPress                | `8080`  |
| mysql        | MariaDB                               | `3306`  |
| phpmyadmin   | Управление БД (опционально)           | `8081`  |
| mailhog      | Перехват исходящих email (опционально) | `8025` |
| wpcli        | WP-CLI (one-shot контейнер)           | —       |

## Возможности

- [x] **WP-CLI** — через отдельный сервис и скрипт `scripts/wp-cli.sh`
- [x] **wp-config.php на хосте** — полный контроль, читает из `.env`
- [x] **WP_DEBUG через .env** — `WP_DEBUG`, `WP_DEBUG_LOG`, `WP_DEBUG_DISPLAY`
- [x] **debug.log на хосте** — `wordpress/wp-content/debug.log`
- [x] **Кастомные плагины** — `wp-content/plugins/custom-plugins/`
- [x] **Кастомные темы** — `wp-content/themes/custom-themes/`
- [x] **Must-use плагины** — `wp-content/mu-plugins/` (автоактивация)
- [x] **Живое редактирование** — `wp-content/` монтирован с хоста
- [x] **Настройки PHP** — `php/php.ini` (`upload_max_filesize`, `memory_limit`, и т.д.)
- [x] **Персистентность БД** — MySQL-данные в именованном volume
- [x] **Конфигурация через .env** — `.env` не коммитится, `.env.example` как шаблон
- [x] **Перехват email** — MailHog/Mailpit (опционально, порт 8025)

## .env

```env
# === Project ===
PROJECT_NAME=mywordpress
COMPOSE_PROJECT_NAME=mywordpress

# === Database ===
DB_NAME=mywordpress_db
DB_USER=mywordpress_user
DB_PASSWORD=change_this_password
DB_ROOT_PASSWORD=change_this_root_password
DB_HOST=mysql
DB_PORT=3306

# === WordPress ===
WP_TABLE_PREFIX=wp_
WP_URL=http://localhost:8080
WP_TITLE="My WordPress Site"
WP_ADMIN_USER=admin
WP_ADMIN_PASSWORD=admin123
WP_ADMIN_EMAIL=admin@example.com
WP_LOCALE=ru_RU

# === Debug ===
WP_DEBUG=true
WP_DEBUG_LOG=true
WP_DEBUG_DISPLAY=false

# === PHP ===
PHP_MAX_EXECUTION_TIME=300
PHP_MEMORY_LIMIT=256M
PHP_UPLOAD_MAX_FILESIZE=64M
PHP_POST_MAX_SIZE=64M

# === Ports ===
WP_PORT=8080
PMA_PORT=8081
MAIL_PORT=8025
MYSQL_EXPOSE_PORT=3306

# === Services ===
ENABLE_PMA=true
ENABLE_MAILHOG=true
```

## FAQ

### Можно ли складывать плагины в подпапку внутри `plugins/`?

Да. WordPress подхватывает плагины из любых поддиректорий внутри `plugins/`. Путь `wp-content/plugins/custom-plugins/my-plugin/my-plugin.php` будет работать. Папка `custom-plugins/` нужна только для удобства разделения — WP найдёт все плагины автоматически.

### Как получить доступ к `wp-config.php` и `wp-settings.php`?

- **`wp-config.php`** — монтируется с хоста (`wordpress/wp-config.php`), полный контроль.
- **`wp-settings.php`** — часть ядра WP, находится внутри контейнера. Менять его не нужно, все настройки делаются через `wp-config.php` и `.env`.

### Как просмотреть debug.log?

Файл `wordpress/wp-content/debug.log` доступен прямо на хосте — не нужно заходить в контейнер. Достаточно включить `WP_DEBUG_LOG=true` в `.env`.
