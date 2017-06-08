#!/bin/sh
cd ~/Applications/sfp-dev;
if [ "$1" = "ssh" ]; then
	ssh ubuntu@`php run.php raw-ip`
else
	php sfp dev $@;
fi
