FROM php:8.4-cli
RUN apt-get update && apt-get install -y --no-install-recommends git unzip libicu-dev libzip-dev libpng-dev libonig-dev libxml2-dev libsqlite3-dev libpq-dev \
    && docker-php-ext-install intl zip gd mbstring bcmath pdo_sqlite pdo_pgsql pcntl \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install dom xml xmlwriter
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /app
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
