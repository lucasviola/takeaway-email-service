APP_NAME=app
DB_NAME=db

build: ## Build the release and develoment container. The development
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

