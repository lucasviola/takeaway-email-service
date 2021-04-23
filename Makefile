APP_NAME=app
DB_NAME=db

build:
	docker compose build --no-cache $(APP_NAME)

start:
	docker compose up -d

migrate:
	docker compose exec $(APP_NAME) php artisan migrate

shell-app:
	docker compose exec $(APP_NAME) /bin/bash

shell-db:
	docker compose exec $(DB_NAME) /bin/bash

install:
	docker compose exec $(APP_NAME) composer install

test:
	php artisan test

stop:
	docker compose down

get-messages:
	docker compose exec $(APP_NAME) php artisan message:get

queue-work:
	docker compose exec $(APP_NAME) php artisan queue:work

setup: migrate queue-work

