# План проекта: WordPress Docker Dev Environment

## Этап 1: Базовая структура Docker-окружения

- [x] Создать структуру директорий (`php/`, `wordpress/`, `mysql/`, `scripts/`)
- [x] `.env` + `.env.example` — конфигурация проекта
- [x] `.gitignore` — исключить `.env`, `debug.log`, данные MySQL
- [x] `docker-compose.yml` — сервисы: wordpress, mysql, wpcli, phpmyadmin, mailhog
- [x] `php/Dockerfile` — кастомный образ на базе `wordpress:latest` + wp-cli
- [x] `php/php.ini` — настройки PHP (подстановка из .env)
- [x] `wordpress/wp-config.php` — конфиг WP, читает переменные из окружения
- [x] `wordpress/wp-content/` — папки: plugins/custom-plugins, themes/custom-themes, mu-plugins
- [x] `scripts/wp-cli.ps1` — PowerShell-скрипт для запуска WP-CLI
- [x] `README.md` — документация по установке и использованию
- [x] `idea.md` — описание структуры и возможностей

## Этап 2: Проверка и тестирование

- [ ] Первый запуск `docker compose up -d` — убедиться, что все сервисы стартуют
- [ ] Проверка WordPress доступен на http://localhost:8080
- [ ] Проверка WP-CLI работает через `.\scripts\wp-cli.ps1`
- [ ] Проверка phpMyAdmin (профиль pma) на http://localhost:8081
- [ ] Проверка MailHog (профиль mail) на http://localhost:8025
- [ ] Проверка debug.log пишется в `wordpress/wp-content/debug.log`
- [ ] Проверка кастомных плагинов/тем подхватываются WP

## Этап 3: Дополнительные улучшения (будущие)

- [ ] Настроить SSL/HTTPS для локальной разработки
- [ ] Добавить xdebug для пошаговой отладки
- [ ] Добавить скрипт бэкапа БД
- [ ] Настроить WP Mail SMTP для перехвата почты через MailHog
- [ ] Добавить Redis/Memcached для кэширования (опционально)
