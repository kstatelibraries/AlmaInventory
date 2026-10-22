# Pinned to 8.2.35: fixes CVE-2026-109618 / CVE-2026-109619 (mod_php RCE). PHP 8.2 is EOL 2026-12-31.
FROM php:8.2.35-apache
# Use production php.ini so PHP notices go to logs instead of corrupting JSON responses
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN a2enmod remoteip \
 && echo "RemoteIPHeader X-Forwarded-For" > /etc/apache2/conf-available/remoteip.conf \
 && echo "RemoteIPInternalProxy 10.0.0.0/8" >> /etc/apache2/conf-available/remoteip.conf \
 && a2enconf remoteip
COPY webapp/* /var/www/html/
COPY php/* /var/www/html/php/
COPY php/barcode.init.js .
