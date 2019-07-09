#!/bin/sh

chmod +x /command.sh
/usr/local/bin/websocketd --port=8888 /command.sh
