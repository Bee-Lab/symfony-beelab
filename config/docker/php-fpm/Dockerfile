FROM composer:2.7 AS composer
FROM php:8.3-fpm

ARG HOST_UID

# Add system packages
RUN apt update && apt install -y --no-install-recommends git unzip ssh

# Set timezone
RUN rm /etc/localtime && ln -s /usr/share/zoneinfo/Europe/Rome /etc/localtime

# Install deps
RUN apt update && apt install -y --no-install-recommends zlib1g-dev libicu-dev g++ vim default-mysql-client libxslt1-dev libxrender1 libxext6 libfontconfig libjpeg-dev libonig-dev nodejs npm
RUN docker-php-ext-install pdo pdo_mysql intl mysqli iconv xsl

# Install xdebug
RUN pecl install xdebug-3.3.2 && docker-php-ext-enable xdebug
RUN { \
      echo 'error_reporting = E_ALL'; \
      echo 'display_startup_errors = On'; \
      echo 'display_errors = On'; \
      echo 'xdebug.mode=debug'; \
      echo 'xdebug.discover_client_host=true'; \
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

# Install phpmyadmin
RUN set -ex; \
    curl -k --output phpMyAdmin.tar.xz --location https://files.phpmyadmin.net/phpMyAdmin/5.2.1/phpMyAdmin-5.2.1-all-languages.tar.xz; \
    tar -xf phpMyAdmin.tar.xz -C /usr/src; \
    rm -r phpMyAdmin.tar.xz; \
    mv /usr/src/phpMyAdmin-5.2.1-all-languages /usr/src/phpmyadmin; \
    rm -rf /usr/src/phpmyadmin/setup/ /usr/src/phpmyadmin/examples/ /usr/src/phpmyadmin/test/ /usr/src/phpmyadmin/po/ /usr/src/phpmyadmin/composer.json /usr/src/phpmyadmin/RELEASE-DATE-5.2.1; \
sed -i "s@define('CONFIG_DIR'.*@define('CONFIG_DIR', '/usr/src/phpmyadmin/');@" /usr/src/phpmyadmin/libraries/vendor_config.php; \
cp /usr/src/phpmyadmin/config.sample.inc.php /usr/src/phpmyadmin/config.inc.php; \
sed -i "s/'localhost'/'database'/" /usr/src/phpmyadmin/config.inc.php; \
sed -i "s/'cookie'/'config'/" /usr/src/phpmyadmin/config.inc.php; \
echo "\$cfg['Servers'][\$i]['password'] = 'root';\n\$cfg['TempDir'] = '/tmp';\n\$cfg['RelationalDisplay'] = 'D';" >> /usr/src/phpmyadmin/config.inc.php;

# Cleanup
RUN apt remove --purge -y $BUILD_PACKAGES && rm -rf /var/lib/apt/lists/*

# Configure user
RUN mkdir /user && useradd -u $HOST_UID -d /user -s /bin/bash user && chown user /user && chown -R user /usr/src/phpmyadmin
USER user
RUN git config --global core.excludesfile ~/.gitignore

COPY php-fpm.conf /etc/php-fpm.conf

WORKDIR /user/customize
