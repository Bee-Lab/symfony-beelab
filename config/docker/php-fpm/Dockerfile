FROM composer:2.10 AS composer
FROM node:24-slim AS node
FROM phpmyadmin:5.2.3-fpm AS phpmyadmin
FROM php:8.5-fpm

ARG HOST_UID

# Set timezone
RUN rm /etc/localtime && ln -s /usr/share/zoneinfo/Europe/Rome /etc/localtime

# Add system packages and install deps
RUN apt update && apt install -y --no-install-recommends \
      git unzip ssh \
      zlib1g-dev libicu-dev vim default-mysql-client libonig-dev bash-completion \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install intl ctype mbstring pdo pdo_mysql

# Install PIE
COPY --from=ghcr.io/php/pie:bin /pie /usr/local/bin/pie

# Install xdebug
RUN pie install xdebug/xdebug && docker-php-ext-enable xdebug \
    && { \
      echo 'error_reporting = E_ALL'; \
      echo 'display_startup_errors = On'; \
      echo 'display_errors = On'; \
      echo 'xdebug.mode=debug'; \
      echo 'xdebug.client_host=host.docker.internal'; \
      echo 'xdebug.file_link_format=xdebug://%f@%l'; \
    } >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Force php timezone
RUN echo "date.timezone = Europe/Rome" >> /usr/local/etc/php/conf.d/docker-php-datetime.ini

# Allow big uploads
RUN { \
      echo 'max_execution_time = 180'; \
      echo 'post_max_size = 100M'; \
      echo 'max_input_vars = 3000'; \
      echo 'upload_max_filesize = 90M'; \
      echo 'memory_limit = -1'; \
    } >> /usr/local/etc/php/conf.d/docker-php-upload.ini

# Install Composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Install Node
COPY --from=node /usr/local/bin /usr/local/bin
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

# Configure completion
COPY sf_completion /etc/bash_completion.d/symfony

# Install phpmyadmin
COPY --from=phpmyadmin /var/www/html /usr/src/phpmyadmin
RUN set -ex; \
rm -rf /usr/src/phpmyadmin/doc /usr/src/phpmyadmin/composer.json /usr/src/phpmyadmin/composer.lock /usr/src/phpmyadmin/package.json /usr/src/phpmyadmin/yarn.lock; \
find /usr/src/phpmyadmin/locale -mindepth 1 -maxdepth 1 -type d ! -name en ! -name it -exec rm -rf {} +; \
find /usr/src/phpmyadmin/js -name '*.map' -delete; \
cp /usr/src/phpmyadmin/config.sample.inc.php /usr/src/phpmyadmin/config.inc.php; \
sed -i "s@'configFile' => .*@'configFile' => '/usr/src/phpmyadmin/config.inc.php',@" /usr/src/phpmyadmin/libraries/vendor_config.php; \
sed -i "s/'localhost'/'database'/" /usr/src/phpmyadmin/config.inc.php; \
sed -i "s/'cookie'/'config'/" /usr/src/phpmyadmin/config.inc.php; \
echo "\$cfg['Servers'][\$i]['password'] = 'root';\n\$cfg['TempDir'] = '/tmp';\n\$cfg['RelationalDisplay'] = 'D';" >> /usr/src/phpmyadmin/config.inc.php;

# Configure user
RUN mkdir /user && useradd -u $HOST_UID -d /user -s /bin/bash user && chown user /user
USER user
RUN git config --global core.excludesfile ~/.gitignore

COPY php-fpm.conf /etc/php-fpm.conf

WORKDIR /user/customize
