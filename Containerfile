FROM php:8.2-apache-bookworm
RUN apt update && apt install git -y
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite headers
RUN git clone https://github.com/4mine05/Web_test.git

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html \
 && chmod -R 775 /var/www/html/uploads

EXPOSE 80
CMD ["apache2ctl", "-D", "FOREGROUND"]
