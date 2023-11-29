FROM php:8.1-apache

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

RUN docker-php-ext-install pdo_mysql mysqli

RUN apt update -y && apt upgrade -y

RUN apt install -y git vim zip unzip

RUN apt-get update \
    && apt-get install -y libpng-dev libjpeg-dev libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip

RUN a2enmod rewrite

RUN service apache2 restart

EXPOSE 80

CMD apache2-foreground
