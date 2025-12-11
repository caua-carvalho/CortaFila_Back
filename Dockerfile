# Imagem base com PHP 8.2 + Apache
FROM php:8.2-apache

# Instalar extensões necessárias (adicione mais se precisar)
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar projeto para dentro do container
COPY . /var/www/html

# Ajustar DocumentRoot para a pasta /public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess com Rewrite
RUN printf "<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" >> /etc/apache2/apache2.conf

# Instalar dependências do Composer dentro do container (PRODUÇÃO)
# Remove dev packages e otimiza autoload
RUN apt-get update && apt-get install -y git unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

WORKDIR /var/www/html

EXPOSE 80

# Iniciar Apache
CMD ["apache2-foreground"]
