FROM dunglas/frankenphp:php8.4-bookworm

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_mysql

RUN composer install --no-dev --optimize-autoloader --no-scripts

CMD ["frankenphp", "run", "--config", "/app/Caddyfile"]