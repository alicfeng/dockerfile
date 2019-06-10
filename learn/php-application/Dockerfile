FROM alpine:edge

COPY default.conf /etc/nginx/conf.d/default.conf

RUN apk update && apk upgrade \
  && apk add ca-certificates \
         nginx \
  && mkdir /www && touch /www/index.html \
      && echo "alicfeng" > /www/index.html


RUN  mkdir /run/nginx && touch /run/nginx/nginx.pid

RUN echo "version 2" > /www/index.html

EXPOSE 80 443

ENTRYPOINT ["nginx", "-g", "daemon off;"]
