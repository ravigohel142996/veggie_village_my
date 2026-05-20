FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev libzip-dev zip unzip \
    && docker-php-ext-install -j"$(nproc)" pdo pdo_mysql mysqli intl zip opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/start-apache.sh /usr/local/bin/start-apache.sh

WORKDIR /var/www/html
COPY . /var/www/html

RUN chmod +x /usr/local/bin/start-apache.sh \
    && chown -R www-data:www-data /var/www/html/images \
    && chmod -R 775 /var/www/html/images

EXPOSE 10000

CMD ["start-apache.sh"]
