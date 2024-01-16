# Usar una imagen de PHP con Apache
FROM php:8.2-apache


RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
 && docker-php-ext-install zip

RUN apt-get update && apt-get install -y git

ENV COMPOSER_ALLOW_SUPERUSER 1

# Habilitar el módulo de reescritura de Apache para Symfony
RUN a2enmod rewrite


RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride None\n\
        Order Allow,Deny\n\
        Allow from All\n\
        FallbackResource /index.php\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar el directorio de la aplicación al contenedor
COPY . /var/www/html/

COPY .env.prod /var/www/html/.env


# Instalar las dependencias con Composer
RUN composer install --no-dev --optimize-autoloader

# Establecer permisos adecuados
RUN chown -R www-data:www-data /var/www/html/
