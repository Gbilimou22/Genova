FROM php:8.2-apache

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier les fichiers
COPY . /var/www/html/

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]