FROM node:6-slim
MAINTAINER AlicFeng <a@samego.com>

ENV VERSION=4.0.0-alpha.6

COPY book.simple.json /opt/gitbook/book.json

RUN npm install --global gitbook-cli -ddd && \
	gitbook fetch ${VERSION} && \
    gitbook init /srv/gitbook && \
    gitbook install && \
	npm cache clear && \
	rm -rf /tmp/*

WORKDIR /srv/gitbook
VOLUME /srv/gitbook /srv/html

EXPOSE 4000 35729

CMD ["gitbook","serve"]
