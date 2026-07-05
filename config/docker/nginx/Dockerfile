FROM phpmyadmin:5.2.3-fpm AS phpmyadmin
FROM nginx:1-alpine

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

COPY nginx.conf /etc/nginx/conf.d/nginx.conf
