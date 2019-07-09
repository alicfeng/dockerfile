FROM alpine:latest

MAINTAINER AlicFeng <a@samego.com>

COPY ./websocketd /usr/local/bin/
COPY ./srv/command.sh /
COPY ./start.sh /

CMD ["/start.sh"]
