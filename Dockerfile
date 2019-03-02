FROM kolemp/docker-php-nginx:7.2

COPY ./ /data/application/
RUN composer install && chown -R www-data:www-data /data/application
