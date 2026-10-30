FROM php:8.3-cli
RUN apt-get update && apt-get install -y git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install bcmath dom mbstring pdo_mysql pdo_sqlite xml xmlreader xmlwriter zip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY . .
EXPOSE 8000
