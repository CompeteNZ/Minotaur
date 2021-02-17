FROM php:7.3-apache

RUN apt-get update -y && apt-get install -y libmcrypt-dev openssl
RUN docker-php-ext-install pdo mbstring

WORKDIR /Minotaur
COPY . /Minotaur

CMD php artisan serve --host=0.0.0.0 --port=8000

EXPOSE 8000