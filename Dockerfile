FROM composer:2.9.2 AS composer

FROM php:8.4.15-fpm

# Common instructions
RUN apt-get update && \
    # Add ru_RU.UTF-8 locale
    apt-get -y install locales && \
    sed -i -e 's/# ru_RU.UTF-8 UTF-8/ru_RU.UTF-8 UTF-8/' /etc/locale.gen && \
    dpkg-reconfigure --frontend=noninteractive locales && \
    update-locale LANG=ru_RU.UTF-8

ENV LANG ru_RU.UTF-8

# Install ext zip
RUN apt-get install -y zip && \
    apt-get install -y libzip-dev && \
    docker-php-ext-install zip

# Install PDO PostgreSQL driver
RUN apt-get install -y libpq-dev && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install pgsql pdo_pgsql

# Install ext intl
RUN apt-get install -y libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# Install ext sockets
RUN docker-php-ext-install sockets

# Install composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
