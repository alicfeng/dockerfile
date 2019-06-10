FROM centos:7

LABEL MAINTAINER="a@samego.com"

# ENV setting
ENV PHP_VERSION      7.1.4
ENV NGINX_VERSION    1.12.2
ENV OPENSSL_VERSION  1.1.0b
ENV PCRE_VERSION     8.41
ENV ZLIB_VERSION     1.2.11
ENV PATH /usr/local/nginx/sbin:$PATH
ENV PATH /usr/local/php/bin:$PATH

COPY supervisord.conf /etc/supervisor/supervisord.conf
COPY index.php /www/default/index.php

# Create user && group
RUN groupadd www && useradd -M -g www -s /sbin/nologin www \
    # Install basic dependence
    && yum update -y \
    && yum install -y \
           git wget \
           unzip \
           gcc gcc-c++ g++ glibc \
           make automake autoconf \
           libtool \
           libxml2-devel \
           openssl openssl-devel \
           curl curl-devel \
           libjpeg-devel \
           libpng libpn-devel \
           freetype-devel \
           gmp-devel \
           libxslt libxslt-devel \
           libXext \
           libXrender \
           xorg-x11-fonts-75dpi \
           xorg-x11-fonts-Type1 \
           python-setuptools \
    # Install supervisor
    && easy_install pip \
    && pip install supervisor \
    && mkdir /etc/supervisor.d \
    # Download source package
    && wget -P /usr/local/src http://cn2.php.net/distributions/php-$PHP_VERSION.tar.gz \
    && wget -P /usr/local/src http://nginx.org/download/nginx-$NGINX_VERSION.tar.gz \
    && wget -P /usr/local/src https://www.openssl.org/source/openssl-$OPENSSL_VERSION.tar.gz \
    && wget -P /usr/local/src https://sourceforge.net/projects/pcre/files/pcre/$PCRE_VERSION/pcre-$PCRE_VERSION.tar.gz \
    && wget -P /usr/local/src https://sourceforge.net/projects/libpng/files/zlib/$ZLIB_VERSION/zlib-$ZLIB_VERSION.tar.gz \
    # Unzip src package
    && tar -xzvf /usr/local/src/php-$PHP_VERSION.tar.gz -C /usr/local/src \
    && tar -zxvf /usr/local/src/pcre-$PCRE_VERSION.tar.gz -C /usr/local/src \
    && tar -zxvf /usr/local/src/zlib-$ZLIB_VERSION.tar.gz -C /usr/local/src \
    && tar -zxvf /usr/local/src/openssl-$OPENSSL_VERSION.tar.gz -C /usr/local/src \
    && tar -zxvf /usr/local/src/nginx-$NGINX_VERSION.tar.gz -C /usr/local/src \
    && rm -rf /usr/local/src/*.tar.gz \
    # Build & Install nginx
    && cd /usr/local/src/nginx-$NGINX_VERSION \
    && ./configure --prefix=/usr/local/nginx \
            --sbin-path=/usr/local/nginx/sbin/nginx \
            --conf-path=/usr/local/nginx/nginx.conf \
            --pid-path=/usr/local/nginx/nginx.pid \
            --user=www \
            --group=www \
            --with-http_ssl_module \
            --with-http_flv_module --with-http_mp4_module \
            --with-http_stub_status_module \
            --with-select_module \
            --with-poll_module \
            --error-log-path=/usr/local/nginx/logs/error.log \
            --http-log-path=/usr/local/nginx/logs/access.log \
            --with-pcre=/usr/local/src/pcre-$PCRE_VERSION \
            --with-zlib=/usr/local/src/zlib-$ZLIB_VERSION \
            --with-openssl=/usr/local/src/openssl-$OPENSSL_VERSION \
            --with-http_v2_module \
    # Make && Make install
    && make && make install \
    && ln -s /usr/local/nginx/sbin/nginx /usr/local/bin/nginx \
    # Build && Install php
    && cd /usr/local/src/php-$PHP_VERSION \
    && ./configure \ 
            --prefix=/usr/local/php \
            --with-curl \
            --with-freetype-dir \
            --with-gd \
            --with-jpeg-dir \
            --with-png-dir \
            --with-zlib-dir  \
            --with-freetype-dir \
            --with-gettext \
            --with-gmp \
            --with-gettext \
            --with-iconv-dir \
            --with-kerberos \
            --with-libdir=lib64 \
            --with-libxml-dir \
            --with-mysqli \
            --with-openssl \
            --with-pcre-regex \
            --with-pdo-mysql \
            --with-pdo-sqlite \
            --with-pear \
            --with-png-dir \
            --with-xmlrpc \
            --with-xsl \
            --with-zlib \
            --enable-fpm \
            --enable-bcmath \
            --enable-libxml \
            --enable-inline-optimization \
            --enable-gd-native-ttf \
            --enable-mbregex \
            --enable-mbstring \
            --enable-opcache \
            --enable-pcntl \
            --enable-shmop \
            --enable-soap \
            --enable-sockets \
            --enable-sysvsem \
            --enable-xml \
            --enable-zip \
    # Make && Make install
    && make && make install \
    # Configure PHP config file
    && cp /usr/local/src/php-$PHP_VERSION/php.ini-development /usr/local/php/lib/php.ini \
    && cp /usr/local/php/etc/php-fpm.conf.default /usr/local/php/etc/php-fpm.conf \
    && cp /usr/local/php/etc/php-fpm.d/www.conf.default /usr/local/php/etc/php-fpm.d/www.conf \
    && cp -R sapi/fpm/php-fpm /etc/init.d/php-fpm \
    && sed -i "s/nobody/www/g" /usr/local/php/etc/php-fpm.d/www.conf \
    && sed -i "s/nobody/www/g" /usr/local/nginx/nginx.conf \
    && mkdir /usr/local/nginx/conf.d -p \
    && cd /usr/local/src \
    # Install composer
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod a+x /usr/local/bin/composer \
    # Install phpredis
    && git clone https://github.com/phpredis/phpredis.git \
    && cd /usr/local/src/phpredis \
    && /usr/local/php/bin/phpize \
    && ./configure --with-php-config=/usr/local/php/bin/php-config \
    && make && make install \
    # Install wkhtmltopdf
    && cd / \
    && wget https://downloads.wkhtmltopdf.org/0.12/0.12.5/wkhtmltox-0.12.5-1.centos7.x86_64.rpm \
    && rpm -ivh --replacefiles wkhtmltox-0.12.5-1.centos7.x86_64.rpm -f \
    && rm wkhtmltox-0.12.5-1.centos7.x86_64.rpm \
    # Install chinese fonts including simsun
    && curl -fSL https://raw.githubusercontent.com/sonatype/maven-guide-zh/master/content-zh/src/main/resources/fonts/simsun.ttc -o  /usr/share/fonts/simsun.ttc \
    # Clean
    && rm -rf /usr/local/src/* \
    && yum remove  -y \
           git \
           wget \
           unzip \
    && yum clean all \
    && rm -rf /tmp/* \
    && yum history new  
    
# Expose port
EXPOSE 80 443
CMD [ "/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf" ]