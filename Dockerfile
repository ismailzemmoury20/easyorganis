FROM dunglas/frankenphp:php8.3-bookworm

WORKDIR /app

COPY . .

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_mysql

RUN composer install --no-dev --optimize-autoloader --no-scripts

CMD ["frankenphp", "run", "--config", "/app/Caddyfile"]
