FROM php:8.2-apache

# Extensiones necesarias para hablar con MySQL desde PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli

# GIT_SHA se recibe como build-arg desde el pipeline de CI/CD;
# sirve para comprobar a simple vista qué commit está desplegado.
ARG GIT_SHA=local
ENV APP_VERSION=$GIT_SHA

COPY src/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
