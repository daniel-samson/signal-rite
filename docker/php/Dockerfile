FROM php:7.2-cli

# Fix Debian Buster EOL repositories
RUN sed -i 's|deb.debian.org|archive.debian.org|g' /etc/apt/sources.list \
 && sed -i 's|security.debian.org|archive.debian.org|g' /etc/apt/sources.list \
 && sed -i '/buster-updates/d' /etc/apt/sources.list \
 && echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid

# Install system deps
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libaio1 \
    wget \
 && docker-php-ext-install \
    intl \
    zip \
    xml \
    mbstring \
 && pecl install xdebug-2.9.8 \
 && docker-php-ext-enable xdebug

# Install Oracle Instant Client and OCI8
ENV ORACLE_HOME=/opt/oracle/instantclient
ENV LD_LIBRARY_PATH=/opt/oracle/instantclient

RUN mkdir -p /opt/oracle \
 && cd /opt/oracle \
 && wget https://download.oracle.com/otn_software/linux/instantclient/2114000/instantclient-basic-linux.x64-21.14.0.0.0dbru.zip \
 && wget https://download.oracle.com/otn_software/linux/instantclient/2114000/instantclient-sdk-linux.x64-21.14.0.0.0dbru.zip \
 && unzip instantclient-basic-linux.x64-21.14.0.0.0dbru.zip \
 && unzip instantclient-sdk-linux.x64-21.14.0.0.0dbru.zip \
 && rm -f *.zip \
 && mv instantclient_21_14 instantclient \
 && echo /opt/oracle/instantclient > /etc/ld.so.conf.d/oracle-instantclient.conf \
 && ldconfig

RUN echo 'instantclient,/opt/oracle/instantclient' | pecl install oci8-2.2.0 \
 && docker-php-ext-enable oci8

# Copy PHP config
COPY docker/php/php.ini /usr/local/etc/php/php.ini

WORKDIR /app

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --2.2 \
 && mv composer.phar /usr/local/bin/composer \
 && rm composer-setup.php

