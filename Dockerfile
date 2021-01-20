FROM php:7.4-cli

RUN apt-get update -y && \
    apt-get upgrade -y && \
    apt-get install -y libmcrypt-dev git zip unzip libzip-dev
RUN docker-php-ext-configure zip

RUN pecl install redis-5.1.1 && \
    pecl install xdebug-2.8.1 && \
    docker-php-ext-enable redis xdebug

COPY .docker/xdebug/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY . /usr/src/dijkstra

WORKDIR /usr/src/dijkstra

# Get Composer!
RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
RUN php /usr/bin/composer install