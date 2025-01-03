FROM php:8.3.3-fpm-alpine3.18
WORKDIR /var/www
RUN apk update

RUN apk add --no-cache git unzip curl bash libpq-dev icu-dev

RUN docker-php-ext-install pdo pdo_pgsql intl opcache

# Устанавливаем Composer и выполняем установку зависимостей
RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" \
                && php /tmp/composer-setup.php --install-dir=/usr/bin --filename=composer \
                && rm /tmp/composer-setup.php

#Установка локали и часового пояса
RUN apk add --no-cache --update icu-libs icu-data-full tzdata
ENV TZ=Europe/Moscow
RUN cp /usr/share/zoneinfo/Europe/Moscow /etc/localtime
