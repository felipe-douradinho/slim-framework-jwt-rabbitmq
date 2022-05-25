## PHP Slim Framework Challenge + PHP 8 + RabbitMQ + JWT Auth (Docker)

## API RESTFul - Stock + Quote System
## Dependency Injection

### Author: <a href="https://www.linkedin.com/in/felipe-douradinho/">Felipe Douradinho</a>

## Features

- JWT Auth: `enabled` (instead of `Basic Auth`)
- MailTrap: `enabled`
- RabbitMQ: `enabled`
- Migrations & Seeding: `Phinx`
- All HTTP requests run on port `8080`
- If the source service (`Stooq`) is down or any kind of error is thrown (network, etc), return the last saved stock quote (with the `live_data=false` flag)
- No unit test was developed due to other challenges in parallel (lack of time / many works)

## Known Issues

- For some reason, `RabbitMQ` container does not work. The error `Connection Refused` was returned even when switching ports, testing ports with telnet, using different IPs, etc. For this reason, `CloudAMQP` is being used just for this challenge

## Settings

- You can choose maximum quote history by changing `MAX_QUOTES_PER_PAGE` in `.env`
- The default `MySQL` settings (from `.env.sample`) works fine (using `root` user just for this challenge)

## Considerations

- We assume that the service consumed from `Stooq` recovers in real time. In production, this could be put in the background (showing a few seconds of old data)

## Tips

- Add the HTTP GET `XDEBUG_SESSION=PHPSTORM` param to enable `XDEBUG`

## Step-by-step to get working:

### 1. Run `docker-compose`:

```shell
docker-compose up -d
```

### 2. Change settings (`MySQL`, `RabbitMQ`, `Mailtrap` etc) in:

Copy the `.env.sample` file into `.env` and modify its contents to match your current settings.

```shell
.env.sample => .env
```

### 3. Install `Composer` Dependencies

```shell
docker-compose exec php8_0 composer install
```

### 4. Run Migrations & Seed

```shell
docker-compose exec php8_0 php vendor/bin/phinx migrate -c config/migrations.php
docker-compose exec php8_0 php vendor/bin/phinx seed:run -c config/migrations.php
```

### 5. Regarding `Postman`

#### 5.1. Import `Postman` JSON collection into your `Postman`

```shell
./PostmanCollection.json
```

#### 5.2. Change requests IP address to point to your local machine

```shell
http://<your_IP>:8080/api/v1/users?XDEBUG_SESSION=PHPSTORM
```


### 6. Register a new user requesting `POST` => `/api/v1/users` endpoint with payload:

```shell
name => string
email => string
password => string (if basic auth usage)
```


### 7. Get a simple quote requesting `GET` => `api/v1/stocks?symbol=aapl.us` endpoint with `Bearer Token` header using the token received in last step 6

### 8. Get the user quotes history requesting `GET` => `api/v1/quotes`
