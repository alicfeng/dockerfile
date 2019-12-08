FROM alpine:3.8
LABEL Maintainer="AlicFeng <a@samego.com>" \
      Description="websocket container based on Alpine Linux"

# env list
ENV TZ=Asia/Shanghai

RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories && \
    apk update && \
    # install base dependence
    apk add --no-cache --virtual .build-deps g++ make autoconf openssl-dev curl && \
    # setting timezone at shanghai
    apk add --no-cache tzdata && \
    cp -rf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime && \
    echo '$TZ' > /etc/timezone && \
    # install php
    apk add --no-cache \
    php7 php7-json php7-openssl php7-curl \
    php7-zlib php7-phar php7-intl \
    php7-ctype php7-session php7-mbstring \
    php7-dev php7-iconv php7-pear \
    # install supervisor
    supervisor && \
    # install swolle
    pecl install swoole && \
    echo "extension=swoole" > /etc/php7/conf.d/00_swoole.ini && \
    # clean
    apk del --no-cache .build-deps && \
    rm -rf /tmp/* /var/cache/apk/* && \
    # create www.www and configuration for application
    addgroup -S www && \
    adduser -D -S -h /var/cache/www -s /sbin/nologin -G www www

# configure php
COPY php/php.ini /etc/php7/conf.d/php.ini
# configure supervisord
COPY supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
# add application src
COPY --chown=www src/ /var/www/
# create container start shell script
COPY start.sh /start.sh

# expose port as well as setting workdir
EXPOSE 5200
WORKDIR /var/www

# start process by supervisord
CMD ["/start.sh"]
