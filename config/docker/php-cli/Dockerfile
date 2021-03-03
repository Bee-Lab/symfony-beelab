FROM php:7.4-cli

ARG HOST_UID

# Add git and unzip
RUN apt update && apt install -y --no-install-recommends git unzip

# Set timezone
RUN rm /etc/localtime && ln -s /usr/share/zoneinfo/Europe/Rome /etc/localtime

# Install extensions
RUN apt update && apt install -y --no-install-recommends zlib1g-dev libicu-dev g++ vim libxslt1-dev libxrender1 libxext6 libfontconfig libjpeg-dev libonig-dev
RUN docker-php-ext-configure intl && docker-php-ext-install pdo pdo_mysql intl xsl

RUN echo 'error_reporting = E_ALL' >> /usr/local/etc/php/conf.d/error-reporting.ini
RUN echo "memory_limit = -1" >> /usr/local/etc/php/conf.d/memory-limit.ini

# Cleanup
RUN apt remove --purge -y $BUILD_PACKAGES && rm -rf /var/lib/apt/lists/*

# Configure user
RUN mkdir /user && useradd -u $HOST_UID -d /user -s /bin/bash user && chown user /user
USER user

WORKDIR /user/app
