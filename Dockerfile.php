# Tracks latest PHP 8.5 patch; rebuild to pick up security fixes (8.5.12+ required for CVE-2026-109618 / CVE-2026-109619).
# PHP 8.5 security support ends 2029-12-31.
FROM php:8.5-apache
# Use production php.ini so PHP errors are never written into JSON responses (warnings/errors are logged to stderr)
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN a2enmod remoteip \
 && echo "RemoteIPHeader X-Forwarded-For" > /etc/apache2/conf-available/remoteip.conf \
 && echo "RemoteIPInternalProxy 10.0.0.0/8" >> /etc/apache2/conf-available/remoteip.conf \
 && a2enconf remoteip
COPY webapp/* /var/www/html/
COPY php/* /var/www/html/php/
COPY php/barcode.init.js .
