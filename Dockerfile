FROM syncxplus/php:7.3.25-apache-buster

LABEL maintainer=jibo@outlook.com

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

