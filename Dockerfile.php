FROM php:8.1.2-apache
RUN a2enmod remoteip \
 && echo "RemoteIPHeader X-Forwarded-For" > /etc/apache2/conf-available/remoteip.conf \
 && echo "RemoteIPInternalProxy 10.0.0.0/8" >> /etc/apache2/conf-available/remoteip.conf \
 && a2enconf remoteip
COPY webapp/* /var/www/html/
COPY php/* /var/www/html/php/
COPY php/barcode.init.js .
