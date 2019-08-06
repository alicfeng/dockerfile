#!/bin/sh

crond -s /var/spool/cron/crontabs -f -L /var/log/cron/cron.log \
&& supervisord --nodaemon --configuration /etc/supervisord.conf
