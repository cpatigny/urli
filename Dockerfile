FROM php:8.3-apache

# Install PHP extensions
RUN apt-get update \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Change Apache document root to backend/src/public
RUN sed -i 's|/var/www/html|/var/www/html/backend/src/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/var/www/html/backend/src/public|g' /etc/apache2/apache2.conf

# Set working directory inside the container
WORKDIR /var/www/html

EXPOSE 80
