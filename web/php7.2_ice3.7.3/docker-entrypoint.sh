#!/usr/bin/env sh

# start crond as well as supervisor service
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf
