FROM unit:php8.2

ENV WEB_DOCUMENT_ROOT=/var/www/app/public
ENV WEB_DOCUMENT_INDEX=index.php
ENV PHP_OPENSSL=yes

WORKDIR /var/www/app

RUN apt-get update && apt-get install -y \
    build-essential \
    libonig-dev \
    libzip-dev \
    git \
    unzip \
    libcurl4-openssl-dev \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN pecl channel-update pecl.php.net

RUN pecl install redis
RUN docker-php-ext-install bcmath pdo_mysql
RUN docker-php-ext-enable redis

COPY ./php/php.ini "$PHP_INI_DIR/php.ini"

COPY ./bashrc /root/.bashrc

COPY ./nginx/* /docker-entrypoint.d/

COPY --chown=unit .././ ./

EXPOSE 80
