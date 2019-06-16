#!/bin/sh

crond -f && supervisord --nodaemon --configuration /etc/supervisord.conf
