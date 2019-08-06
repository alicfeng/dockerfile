FROM composer:1.7

LABEL maintainer="a@samego.com"

RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories \
    && apk add --no-cache tini\
    && apk add git \
    && mkdir -p /src/app \
    && git clone https://github.com/erikdubbelboer/phpRedisAdmin.git /src/app \
    && cd /src/app \
    && composer install -vvv \
    && cp includes/config.environment.inc.php includes/config.inc.php

RUN cd  /src/app && ls && pwd
WORKDIR  /src/app

EXPOSE 80

ENTRYPOINT [ "tini", "--", "php", "-S", "0.0.0.0:80" ]