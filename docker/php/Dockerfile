FROM php:8.1-fpm-alpine

#vite の npm run dev 時のポート対応
EXPOSE 5173

# install composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# install packages
RUN apk update

# apk add を複数行に分けないと正常にインストールされないためにわけました
RUN apk add zip unzip git
RUN apk add nodejs npm

# install php extensions
RUN docker-php-ext-install pdo pdo_mysql