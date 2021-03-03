FROM nginx:1

RUN apt update && apt install -y --no-install-recommends curl

# Install phpmyadmin
RUN set -ex; \
    curl -k --output phpMyAdmin.tar.gz --location https://files.phpmyadmin.net/phpMyAdmin/5.0.4/phpMyAdmin-5.0.4-all-languages.tar.gz; \
    tar -xf phpMyAdmin.tar.gz -C /usr/src; \
    rm -r phpMyAdmin.tar.gz; \
    mv /usr/src/phpMyAdmin-5.0.4-all-languages /usr/src/phpmyadmin; \
    rm -rf /usr/src/phpmyadmin/setup/ /usr/src/phpmyadmin/examples/ /usr/src/phpmyadmin/test/ /usr/src/phpmyadmin/po/ /usr/src/phpmyadmin/composer.json /usr/src/phpmyadmin/RELEASE-DATE-5.0.4; \
sed -i "s@define('CONFIG_DIR'.*@define('CONFIG_DIR', '/usr/src/phpmyadmin/');@" /usr/src/phpmyadmin/libraries/vendor_config.php; \
cp /usr/src/phpmyadmin/config.sample.inc.php /usr/src/phpmyadmin/config.inc.php; \
sed -i "s/'localhost'/'database'/" /usr/src/phpmyadmin/config.inc.php; \
sed -i "s/'cookie'/'config'/" /usr/src/phpmyadmin/config.inc.php; \
echo "\$cfg['Servers'][\$i]['password'] = 'root';\n\$cfg['TempDir'] = '/tmp';" >> /usr/src/phpmyadmin/config.inc.php;

COPY nginx.conf /etc/nginx/conf.d/nginx.conf

# Cleanup
RUN apt remove --purge -y $BUILD_PACKAGES && rm -rf /var/lib/apt/lists/*
