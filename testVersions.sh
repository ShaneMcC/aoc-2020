#!/bin/bash

cd "$(dirname "$0")"

ONLYDAY=""
if [ "${1}" != "" ]; then
	ONLYDAY="${1}"
fi;

for DAY in `seq 1 25`; do
	if [ "${ONLYDAY}" != "" -a "${ONLYDAY}" != "${DAY}" ]; then
		continue;
	fi;

	if [ -e ${DAY} ]; then
		echo "Day ${DAY}:"

		echo -n '       php74: ';
		./docker.sh --php74 --time ${DAY} | grep -i real;

		echo -n '        php8: ';
		./docker.sh --php8 --time ${DAY} | grep -i real;

		echo -n '    php8-jit: ';
		./docker.sh --php8-jit --time ${DAY} | grep -i real;

		echo ""
	fi;
done;
