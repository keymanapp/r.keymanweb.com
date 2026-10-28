# syntax=docker/dockerfile:1

# Site
FROM php:8.4-apache@sha256:56e7dab56ae21ec10aae951f93cbe7069c1ca68a714dd22ca78724268c78f5de
COPY resources/keyman-site.conf /etc/apache2/conf-available/
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
RUN chown -R www-data:www-data /var/www/html/
RUN a2enmod rewrite headers proxy proxy_http ssl; a2enconf keyman-site
