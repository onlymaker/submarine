FROM syncxplus/php:7.3.11-apache-stretch

LABEL maintainer=jibo@outlook.com

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

