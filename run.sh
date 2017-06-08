#!/bin/sh
cd ~/Applications/sfp-dev;
if [ "$1" = "ssh" ]; then
	ssh ubuntu@`php sfp dev raw-ip`
else
	php sfp dev $@;
fi
