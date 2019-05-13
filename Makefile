SHELL=/bin/bash
DOCKER_COMPOSE ?= docker-compose
DOCKER_COMPOSE_EXEC_PHP = $(DOCKER_COMPOSE) exec php-cli

include .env

.DEFAULT_GOAL := help

.PHONY: help
help:
	grep -E '^[a-zA-Z-]+:.*?## .*$$' Makefile | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "[32m%-12s[0m %s\n", $$1, $$2}'

.PHONY: test
test: test-phpcs test-phpunit test-behat ## Run tests

test-phpcs: ## Run PHPCS tests
	$(DOCKER_COMPOSE_EXEC_PHP) vendor/bin/phpcs -p ./src -p ./tests --standard=PHPCompatibility,PSR2 --runtime-set testVersion 5.6-

test-phpunit: ## Run PHPUNIT tests
	$(DOCKER_COMPOSE_EXEC_PHP) vendor/bin/phpunit -c phpunit.xml

test-behat: ## Run BEHAT tests
	$(DOCKER_COMPOSE_EXEC_PHP) vendor/bin/behat

.PHONY: start_dev
start_dev: ## Start DEV in Docker
	$(DOCKER_COMPOSE) up -d --build

.PHONY: stop_dev
stop_dev: ## Stop DEV in Docker
	$(DOCKER_COMPOSE) stop

.PHONY: restart_dev
restart_dev: ## Restart DEV in Docker
	$(DOCKER_COMPOSE) restart

.PHONY: down_dev
down_dev: ## Remove DEV containers from Docker
	$(DOCKER_COMPOSE) down

.PHONY: export-mysql-database
export-mysql-database: # Export mysql initial database from current DB
	mysqldump -u root -p${DOCKER_MYSQL_PASSWORD} -P ${DOCKER_MYSQL_LOCAL_PORT} -h 127.0.0.1 --no-data --databases light_orm_mysql > .docker/config/mysql/light_orm_mysql.sql
