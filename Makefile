APP_NAME=app
DB_NAME=db

build:
	docker compose build --no-cache $(APP_NAME)

run:
	docker compose up -d

configure:
	docker compose exec $(APP_NAME) php artisan migrate

shell-app:
	docker compose exec $(APP_NAME) /bin/bash

shell-db:
	docker compose exec $(DB_NAME) /bin/bash

stop:
	docker compose down

get-messages:
	docker compose exec $(APP_NAME) php artisan message:get

