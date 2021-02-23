#FROM php:7.4.15-cli /brak kontenera php w docker ps - invoice-php exited with code 0 :<
FROM php:7.4.14-fpm

COPY --from=composer:1.9 /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql \
    && docker-php-ext-install pdo pdo_mysql
RUN pecl install xdebug \
    && pecl install apcu-5.1.19
RUN  usermod -u 1000 www-data
RUN docker-php-ext-install \
        mysqli \
        pdo_mysql \
        opcache

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp
