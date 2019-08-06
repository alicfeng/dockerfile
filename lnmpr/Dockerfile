FROM alpine:latest

MAINTAINER AlicFeng <a@samego.com>

# Environments
ENV TIMEZONE            Asia/Shanghai
ENV PHP_MEMORY_LIMIT    512M
ENV MAX_UPLOAD          50M
ENV PHP_MAX_FILE_UPLOAD 200
ENV PHP_MAX_POST        100M
ENV COMPOSER_ALLOW_SUPERUSER 1

COPY ./www /www
COPY ./nginx/conf.d/default.conf /etc/nginx/conf.d/
COPY ./supervisor.d /etc/supervisor.d/
COPY ./etc/crontab /etc/crontabs/root


RUN \
# use aliyun mirrors && update
sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories \
&& apk update \
&& apk upgrade \

# install php
&& apk add \
    curl \
    tzdata \
    php7-fpm\
    php7 \
    php7-dev \
    php7-apcu \
    php7-bcmath \
    php7-xmlwriter \
    php7-ctype \
    php7-curl \
    php7-exif \
    php7-iconv \
    php7-intl \
    php7-json \
    php7-mbstring\
    php7-opcache \
    php7-openssl \
    php7-pcntl \
    php7-pdo \
    php7-mysqlnd \
    php7-mysqli \
    php7-pdo_mysql \
    php7-pdo_pgsql \
    php7-phar \
    php7-posix \
    php7-session \
    php7-xml \
    php7-simplexml \
    php7-mcrypt \
    php7-xsl \
    php7-zip \
    php7-zlib \
    php7-dom \
    php7-redis\
    php7-tokenizer \
    php7-gd \
    php7-mongodb\
    php7-fileinfo \
    php7-zmq \
    php7-memcached \
    php7-xmlreader \
# composer
&& curl -sS https://getcomposer.org/installer | \
php -- --install-dir=/usr/bin/ --filename=composer \

# set timezone
&& cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
&& echo "${TIMEZONE}" > /etc/timezone \

# install nginx
&& apk add nginx \
&& mkdir /run/nginx \
&& touch /run/nginx/nginx.pid \
&& sed -i "s/nobody/nginx/g" /etc/php7/php-fpm.d/www.conf \

# install supervisor
&& apk add supervisor \
&& mkdir -p /etc/supervisor.d/ \

# 赋予权限
&& chmod -R a+w /www \
&& chmod -R a+w /var/log \

# clean
&& apk del tzdata -f \
&& rm -rf /tmp/* /var/cache/apk/* \

# set environments
&& sed -i "s|;*date.timezone =.*|date.timezone = ${TIMEZONE}|i" /etc/php7/php.ini \
&& sed -i "s|;*memory_limit =.*|memory_limit = ${PHP_MEMORY_LIMIT}|i" /etc/php7/php.ini \
&& sed -i "s|;*upload_max_filesize =.*|upload_max_filesize = ${MAX_UPLOAD}|i" /etc/php7/php.ini \
&& sed -i "s|;*max_file_uploads =.*|max_file_uploads = ${PHP_MAX_FILE_UPLOAD}|i" /etc/php7/php.ini \
&& sed -i "s|;*post_max_size =.*|post_max_size = ${PHP_MAX_POST}|i" /etc/php7/php.ini \
&& sed -i "s|;*cgi.fix_pathinfo=.*|cgi.fix_pathinfo= 0|i" /etc/php7/php.ini

COPY start.sh /

CMD ["/start.sh"]
