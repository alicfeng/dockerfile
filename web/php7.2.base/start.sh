#!/usr/bin/env sh

# start crond as well as supervisor service
crond && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
