APP_NAME=app

build: ## Build the release and develoment container. The development
	docker compose build --no-cache $(APP_NAME)

run:
	docker compose up -d

configure:
	docker compose exec $(APP_NAME) php artisan migrate

stop:
	docker compose down

