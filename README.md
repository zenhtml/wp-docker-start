# WordPress Docker Dev Environment

A local WordPress development environment on Docker with WP-CLI, debugging, and live file editing.

## Requirements

- [Docker](https://www.docker.com/) + Docker Compose

## Quick Start

```powershell
# 1. Copy configuration
copy .env.example .env

# 2. Edit .env (passwords, ports, project name)
notepad .env

# 3. Start services
docker compose up -d

# 4. Install WordPress (first run)
.\scripts\wp-cli.ps1 core install `
    --url=http://localhost:8080 `
    --title="My WordPress Site" `
    --admin_user=admin `
    --admin_password=admin123 `
    --admin_email=admin@example.com
```

After starting, open http://localhost:8080

## Project Structure

```
docker-web/
├── .env                         # Configuration (not committed)
├── .env.example                 # Configuration template
├── .gitignore
├── Makefile                     # Make shortcuts
├── docker-compose.yml
├── php/
│   ├── Dockerfile               # PHP-Apache + wp-cli
│   └── php.ini                  # PHP settings
├── wordpress/
│   ├── wp-config.php            # WP config (reads from .env)
│   └── wp-content/
│       ├── debug.log            # Debug log (on host)
│       ├── plugins/custom-plugins/   # Custom plugins
│       ├── themes/custom-themes/     # Custom themes
│       └── mu-plugins/               # Must-use plugins
├── mysql/                       # SQL dumps for DB initialization
└── scripts/
    └── wp-cli.ps1               # WP-CLI shortcut (PowerShell)
```

## Services

| Service    | Description                   | URL/Port                    |
| ---------- | ----------------------------- | --------------------------- |
| wordpress  | PHP-Apache + WordPress        | http://localhost:8080       |
| mysql      | MariaDB 11                    | localhost:3306              |
| wpcli      | WP-CLI (on demand)            | —                           |
| phpmyadmin | Database management (opt.)    | http://localhost:8081       |
| mailhog    | Email interception (opt.)     | http://localhost:8025       |

## Commands

### Make (recommended)

```bash
make up          # Start services
make down        # Stop services
make ps          # Show container status
make logs        # Tail logs in real time
make wp cmd='plugin list'   # WP-CLI
make install     # Install WordPress
make pma         # Start with phpMyAdmin
make mail        # Start with MailHog
make full        # Start everything (phpMyAdmin + MailHog)
make fresh       # Full reset (delete volumes)
```

### Docker Compose

```powershell
# Core services (wordpress + mysql)
docker compose up -d

# With phpMyAdmin
docker compose --profile pma up -d

# With MailHog
docker compose --profile mail up -d

# Everything
docker compose --profile pma --profile mail up -d

# Stop
docker compose down

# Stop and remove DB data
docker compose down -v
```

### WP-CLI

```powershell
# List plugins
.\scripts\wp-cli.ps1 plugin list

# Install a plugin
.\scripts\wp-cli.ps1 plugin install woocommerce --activate

# List themes
.\scripts\wp-cli.ps1 theme list

# Regenerate .htaccess
.\scripts\wp-cli.ps1 rewrite structure '/%postname%/'

# Export database
.\scripts\wp-cli.ps1 db export - > backup.sql

# Verify core checksums
.\scripts\wp-cli.ps1 core verify-checksums
```

## Configuration

All settings are defined in `.env` (copy from `.env.example`):

| Variable               | Description               | Default                   |
| ---------------------- | ------------------------- | ------------------------- |
| `PROJECT_NAME`         | Project name              | `mywordpress`             |
| `DB_NAME`              | Database name             | `mywordpress_db`          |
| `DB_USER`              | Database user             | `mywordpress_user`        |
| `DB_PASSWORD`          | Database password         | `change_this_password`    |
| `DB_ROOT_PASSWORD`     | Root password             | `change_this_root_password` |
| `WP_TABLE_PREFIX`      | Table prefix              | `wp_`                     |
| `WP_DEBUG`             | Enable WP debugging       | `true`                    |
| `WP_DEBUG_LOG`         | Write to debug.log        | `true`                    |
| `WP_DEBUG_DISPLAY`     | Show errors on screen     | `false`                   |
| `PHP_MEMORY_LIMIT`     | PHP memory limit          | `256M`                    |
| `PHP_UPLOAD_MAX_FILESIZE` | Max upload size         | `64M`                     |
| `WP_PORT`              | WordPress port            | `8080`                    |
| `PMA_PORT`             | phpMyAdmin port           | `8081`                    |
| `MAIL_PORT`            | MailHog port              | `8025`                    |

## Development

### Custom Plugins

Create a plugin folder inside `wordpress/wp-content/plugins/custom-plugins/`:

```
wordpress/wp-content/plugins/custom-plugins/
└── my-plugin/
    └── my-plugin.php
```

WordPress will automatically detect the plugin — it will appear in the admin panel.

### Custom Themes

Same approach — inside `wordpress/wp-content/themes/custom-themes/`:

```
wordpress/wp-content/themes/custom-themes/
└── my-theme/
    ├── style.css
    ├── index.php
    └── functions.php
```

### Must-use Plugins

Files in `wordpress/wp-content/mu-plugins/` are activated automatically (cannot be disabled from the admin panel).

### Debugging

With `WP_DEBUG_LOG=true`, WordPress writes logs to `wordpress/wp-content/debug.log` — the file is available on the host, no need to enter the container.

```powershell
# Watch log in real time
Get-Content wordpress\wp-content\debug.log -Wait -Tail 50
```

## FAQ

**Q: How to import an existing database?**

Place an SQL dump in `mysql/init.sql` — it will run on first MariaDB container start. Or use WP-CLI: `.\scripts\wp-cli.ps1 db import backup.sql`

**Q: How to reset everything and start over?**

```powershell
docker compose down -v
docker compose up -d
```

The `-v` flag removes volumes with DB data and WP core files.
