FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        default-mysql-client \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
    && docker-php-ext-install \
        bcmath \
        mbstring \
        pdo_mysql \
        zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/app/start.sh /usr/local/bin/start-hospital-his
RUN chmod +x /usr/local/bin/start-hospital-his

EXPOSE 8000

CMD ["start-hospital-his"]
