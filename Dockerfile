FROM php:8.2-apache

# Instala dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    unzip git libpq-dev libonig-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia todo o projeto
COPY . /var/www/html

# Instala dependências PHP do projeto
RUN composer install --no-dev --optimize-autoloader

# Ativa mod_rewrite
RUN a2enmod rewrite

# Ajusta DocumentRoot para /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Substitui DocumentRoot no config do Apache
RUN sed -ri \
    -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" \
    /etc/apache2/sites-available/000-default.conf

# Garante que o .htaccess funcione em /public
RUN sed -ri \
    -e 's/AllowOverride None/AllowOverride All/g' \
    /etc/apache2/apache2.conf

# Porta dinâmica do Render
ARG PORT
ENV PORT=${PORT}

# Ajusta Apache para escutar na porta do Render
RUN sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf \
    && sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-enabled/000-default.conf

# Expõe a porta (opcional, Render ignora)
EXPOSE ${PORT}

# Inicializa Apache
CMD ["apache2-foreground"]
