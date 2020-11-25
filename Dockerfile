FROM php:7.4

ENV MEMCACHED_DEPS libz-dev libmemcached-dev
RUN apt-get update \
    && apt-get install -y git unzip $MEMCACHED_DEPS \
    && pecl install memcached \
    && pecl install igbinary \
    && pecl install xdebug \
    && docker-php-ext-enable memcached \
    && docker-php-ext-enable igbinary \
    && docker-php-ext-enable xdebug

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

VOLUME ["/opt/app"]

WORKDIR /opt/app
