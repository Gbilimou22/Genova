# Utiliser l'image PHP avec Apache
FROM php:8.2-apache

# Mettre à jour et installer les dépendances système
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    gd \
    mysqli \
    zip

# Activer mod_rewrite
RUN a2enmod rewrite

# Configurer le document root
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copier les fichiers du projet
COPY . /var/www/html/

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Activer les logs d'erreur
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/errors.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini

EXPOSE 80

CMD ["apache2-foreground"]