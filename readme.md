# Takeaway Email Service

This is an e-mail notification service.

## Settip up the app

### What do you need?
1. Docker and Docker compose

## For the first execution
1. Run the app with docker compose
```
docker-compose build app
docker-compose up -d
```
2. Run composer in the app so to install the dependencies
```
docker-compose exec app composer install
```

3. Run artisan to generate an encryption key (TODO: remove)
```
docker-compose exec app php artisan key:generate
```

## How to run the app
```
docker-compose build app
docker-compose up -d
```

## Other


