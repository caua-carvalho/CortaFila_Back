FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    unzip git libpq-dev libonig-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copia tudo pro container
COPY . /var/www/html

# Instala dependências
RUN composer install --no-dev --optimize-autoloader

# Ativa rewrite
RUN a2enmod rewrite

# Ajusta DocumentRoot para a pasta /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Substitui DocumentRoot no config do Apache
RUN sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf

# Opcional: garante que o .htaccess funciona no /public
RUN sed -ri \
    -e 's/AllowOverride None/AllowOverride All/g' \
    /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]
