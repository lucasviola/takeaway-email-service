# Takeaway Email Service

This is an e-mail notification service.

This was built with:
* PHP 7.4
* Laravel 7
* Mysql
* RabbitMQ

### What do you need?
1. Docker and Docker compose
2. Make (optional)

## How to run the app
1. Run the app with docker compose
```shell
docker compose build app
docker compose up -d
```
2. Install migrations
```shell
docker compose exec php artisan migrate
```

Or if you have make installed you can run
```shell
$ make build
$ make start
$ make migrate
```

## Other useful make commands
1. Execute shell into the application
```shell
$ make shell-app
```
2. Execute shell into the database
```shell
$ make shell-db
```
3. Stop docker compose
```shell
$ make stop
```
4. Install dependencies
```shell
$ make install
```
5. Retrieves all the messages that got sent
```shell
$ make get-messages
```
## API Endpoints
This API has 2 endpoints. One for sending a message and another one
for retrieving all messages. They are documented as below.

```http
$ POST /messages
202 ACCEPTED
```
### Request
```json
{
    "from": {
        "name": "Lucas",
        "email": "lucasmatzenbacher@gmail.com"
    },
    "to": {
        "name": "Lucas",
        "email": "lucasmatzenbacher@gmail.com"
    },
    "subject": "it worked",
    "message": "test"
}
```
### Response
```json
{
    "messageId": "607fbe5196aba",
    "messageStatus": "Queued"
}
```
* IMPORTANT: Check spam folder if you did not receive the message in your inbox.

```http
$ GET /messages
202 ACCEPTED
```
### Response
```json
[
    {
        "id": 1,
        "messageId": "607fbe5196aba",
        "to": "lucasmatzenbacher@gmail.com",
        "from": "lucasmatzenbacher@gmail.com",
        "subject": "it worked",
        "message": "test",
        "created_at": "2021-04-21T05:55:32.000000Z",
        "updated_at": "2021-04-21T05:55:32.000000Z"
    }
]
```
## Architecture Documentation

## Things I would improve



