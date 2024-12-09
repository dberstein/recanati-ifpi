FROM php:8.4-apache
RUN apt update -y && apt upgrade -y \
 && a2enmod rewrite && a2enmod actions \
# Install composer requirements \
 && apt install -y libzip-dev/stable \
 && docker-php-ext-install zip

# Configure PHP
RUN mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
 && sed -i'' 's/expose_php = On/expose_php = Off/g' /usr/local/etc/php/php.ini \
 && sed -i'' 's/max_execution_time = 30/max_execution_time = 180/g' /usr/local/etc/php/php.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/

# Run composer (code must autoload using PSR-4)
COPY ./composer.json ./composer.json
RUN composer update --no-dev

# Copy source code
COPY ./src/ ./
RUN composer dumpautoload --optimize