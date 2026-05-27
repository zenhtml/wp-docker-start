.PHONY: help up down restart ps logs wp pma mail build clean install fresh

help:
	@echo ""
	@echo "WordPress Docker Dev Environment"
	@echo ""
	@echo "  make up         Start all services"
	@echo "  make down       Stop all services"
	@echo "  make restart    Restart all services"
	@echo "  make ps         Show container status"
	@echo "  make logs       Tail all logs (Ctrl+C to stop)"
	@echo "  make build      Rebuild images"
	@echo "  make wp         Run WP-CLI (usage: make wp cmd='plugin list')"
	@echo "  make install    Run WordPress installation"
	@echo "  make pma        Start with phpMyAdmin"
	@echo "  make mail       Start with MailHog"
	@echo "  make full       Start with phpMyAdmin + MailHog"
	@echo "  make clean      Stop and remove containers, keep DB data"
	@echo "  make fresh      Full reset including DB data"
	@echo ""

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

ps:
	docker compose ps

logs:
	docker compose logs -f

build:
	docker compose build

wp:
	docker compose run --rm wpcli $(cmd)

install:
	docker compose run --rm wpcli core install \
		--url=$${WP_URL:-http://localhost:8080} \
		--title="$${WP_TITLE:-WordPress}" \
		--admin_user=$${WP_ADMIN_USER:-admin} \
		--admin_password=$${WP_ADMIN_PASSWORD:-admin123} \
		--admin_email=$${WP_ADMIN_EMAIL:-admin@example.com}

pma:
	docker compose --profile pma up -d

mail:
	docker compose --profile mail up -d

full:
	docker compose --profile pma --profile mail up -d

clean:
	docker compose down

fresh:
	docker compose down -v
