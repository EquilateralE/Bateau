# Image de production — le code PHP est embarqué dans l'image
FROM php:8.2-apache

# Installation de l'extension PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copie du code applicatif dans l'image (code figé au moment du build)
COPY app/index.php /var/www/html/index.php

# Variable d'environnement de production
ENV APP_ENV=production

EXPOSE 80
