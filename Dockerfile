FROM syncxplus/php:7.2.13-apache-stretch

LABEL maintainer='jibo@outlook.com'

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

