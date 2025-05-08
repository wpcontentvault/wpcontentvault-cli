FROM php:8.4-cli-bookworm

ARG USER_ID=1000
ARG GROUP_ID=1000

ENV WPPOSTVAULT_LOG_LEVEL=debug
ENV WPPOSTVAULT_DATA_PATH=/var/www/data
ENV WPPOSTVAULT_VAULT_PATH=/var/www/vault

RUN apt update && apt install -y $PHPIZE_DEPS libonig-dev libpq-dev libzip-dev libsqlite3-dev libicu-dev bash vim git unzip

RUN docker-php-ext-install mbstring pdo pdo_mysql mysqli zip intl pcntl

#Needed for image conversion during import
RUN apt update && apt install -y libwebp-dev libfreetype-dev libjpeg-dev libpng-dev
RUN docker-php-ext-configure gd --with-webp --with-freetype --with-jpeg 
RUN docker-php-ext-install gd exif

RUN docker-php-ext-enable opcache

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -L https://github.com/box-project/box/releases/download/4.6.6/box.phar --output /usr/local/bin/box
RUN chmod ugo+x /usr/local/bin/box

#Change UID and GID for www-data to local user UID/GID
RUN usermod -u ${USER_ID} www-data
RUN groupmod -g ${GROUP_ID} www-data

WORKDIR /var/www

COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

RUN apt-get install -y iputils-ping

USER www-data

ENTRYPOINT ["/entrypoint.sh"]
