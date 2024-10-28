FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \                    
    curl \
    libpq-dev \       
    zip \                   
    unzip    

RUN apt-get update && docker-php-ext-install pdo pdo_pgsql


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini



WORKDIR /var/www

COPY ../src/public .

CMD [ "php-fpm" ]

EXPOSE 9000/tcp
