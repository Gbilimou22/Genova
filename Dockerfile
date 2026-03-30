# Dockerfile simplifié pour SQLite
FROM php:8.2-apache

# Installer les extensions de base (pas besoin de PostgreSQL)
RUN docker-php-ext-install pdo_mysql mysqli

# Activer mod_rewrite
RUN a2enmod rewrite

# Configurer le document root
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copier les fichiers
COPY . /var/www/html/

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod 777 /var/www/html/database.sqlite

EXPOSE 80

CMD ["apache2-foreground"]