FROM php:8.4-apache
# Update Debian
RUN apt update -y && apt upgrade -y \
# Apache modules \
 && a2enmod rewrite && a2enmod actions \
# Install composer requirements \
 && apt install -y libzip-dev/stable \
 && docker-php-ext-install zip \
# Configure PHP \
 && cd /usr/local/etc/php && ln -nsf php.ini-production php.ini \
 && sed -i'' 's/expose_php = On/expose_php = Off/g' php.ini \
 && sed -i'' 's/max_execution_time = 30/max_execution_time = 45/g' php.ini \
# Install Composer \
 && curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/

# Run composer (code must autoload using PSR-4)
COPY ./composer.json ./composer.json
RUN composer update --no-dev

# Copy source code
COPY ./src/ ./
RUN composer dumpautoload --optimize