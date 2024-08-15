PROJECT_ROOT := $(shell pwd)
DOCKER_DIR := $(PROJECT_ROOT)/docker
APP_DIR := $(PROJECT_ROOT)/app

.PHONY: all
all: build up

.PHONY: build
build:
	@echo "Building Docker images..."
	docker-compose -f $(DOCKER_DIR)/docker-compose.yml build

.PHONY: up
up:
	@echo "Starting Docker containers..."
	docker-compose -f $(DOCKER_DIR)/docker-compose.yml up -d --build

.PHONY: down
down:
	@echo "Stopping Docker containers..."
	docker-compose -f $(DOCKER_DIR)/docker-compose.yml down -v

.PHONY: clean
clean:
	@echo "Cleaning up Docker containers and images..."
	docker-compose -f $(DOCKER_DIR)/docker-compose.yml down --volumes --rmi all

.PHONY: composer-install
composer-install:
	@echo "Running composer install..."
	docker-compose -f $(DOCKER_DIR)/docker-compose.yml exec php composer install --prefer-dist --no-progress --no-interaction

.PHONY: composer-update
composer-update:
	@echo "Running composer update..."
	docker-compose -f $(DOCKER_DIR)/docker-compose.yml exec php composer update --prefer-dist --no-progress --no-interaction

.PHONY: composer-dump-autoload
composer-dump-autoload:
	@echo "Running composer dump-autoload..."
	docker-compose -f $(DOCKER_DIR)/docker-compose.yml exec php composer dump-autoload --optimize

.PHONY: migrate
migrate:
	@echo "Running database migrations..."
	docker-compose -f $(DOCKER_DIR)/docker-compose.yml exec php php bin/console doctrine:migrations:migrate --no-interaction

.PHONY: test
test:
	@echo "Running tests..."
	docker-compose -f $(DOCKER_DIR)/docker-compose.yml exec php vendor/bin/phpunit

.PHONY: bash
bash:
	@echo "Entering bash shell in the php container..."
	docker-compose -f $(DOCKER_DIR)/docker-compose.yml exec php bash

.PHONY: install
bash:
	@echo "Install project.."
	make up
	make migrate

