FROM php:8.5-apache

# Installer l'extension PDO MySQL
RUN docker-php-ext-install pdo_mysql

# Activer le module Apache pour le routage MVC
RUN a2enmod rewrite

# Configurer Apache pour utiliser le dossier public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/000-default.conf

# Autoriser les fichiers .htaccess
RUN printf '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/app.conf \
    && a2enconf app

# Utiliser le port 8080
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' \
    /etc/apache2/sites-available/000-default.conf

# Copier le projet
WORKDIR /var/www/html
COPY . .

# Préparer les dossiers et permissions pour www-data
RUN mkdir -p /var/run/apache2 /var/lock/apache2 \
    && chown -R www-data:www-data /var/www/html \
       /var/run/apache2 /var/lock/apache2 /var/log/apache2

# Ne pas exécuter Apache en root
USER www-data

EXPOSE 8080

# Vérifier que l'application et la base répondent
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD ["php", "-r", "$h = get_headers('http://127.0.0.1:8080/health'); exit($h && str_contains($h[0], '200') ? 0 : 1);"]

CMD ["apache2-foreground"]
