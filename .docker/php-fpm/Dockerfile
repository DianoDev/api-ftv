FROM gitlab.tce.mt.gov.br:5050/publico/php:8.2-fpm-latest

USER root

# COPY --from=composer/composer:2.8.3-bin /composer /usr/bin/composer

COPY --chown=www:www-data .docker/php-fpm/php.ini /usr/local/etc/php/php.ini
COPY --chown=www:www-data .docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

COPY --chown=www:www-data . /var/www/html
RUN rm -rf /var/www/html/.docker

# Ajusta as permissões dos diretórios e arquivos
RUN find /var/www/html \( -path /var/www/html/storage -o -path /var/www/html/vendor \) -prune -o -type d -exec chmod 755 {} +
RUN find /var/www/html \( -path /var/www/html/storage -o -path /var/www/html/vendor \) -prune -o -type f -exec chmod 644 {} +
RUN find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} +

USER www

WORKDIR /var/www/html

EXPOSE 9000
