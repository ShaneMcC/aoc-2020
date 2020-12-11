#!/bin/bash

cd "$(dirname "$0")"

EXIT=0;

ONLYDAY=""
if [ "${1}" != "" ]; then
	ONLYDAY="${1}"
fi;

for DAY in `seq 1 25`; do
	if [ "${ONLYDAY}" != "" -a "${ONLYDAY}" != "${DAY}" ]; then
		continue;
	fi;

	if [ -e ${DAY} ]; then
		echo -n "Day ${DAY}:"
		if [ -e ${DAY}/answers.txt -a $(cat ${DAY}/answers.txt 2>/dev/null | wc -l) -ne 0 ]; then
			PART1=$(cat ${DAY}/answers.txt | head -n 1)
			if [ $(cat ${DAY}/answers.txt | wc -l) -eq 1 ]; then
				PART2=""
			else
				PART2=$(cat ${DAY}/answers.txt | head -n 2 | tail -n 1)
			fi;

			RESULT=$(${DAY}/run.php 2>/dev/null | grep -Pzl "(?s).*${PART1}.*\n.*${PART2}.*")

			if [ "${RESULT}" = "" ]; then
				echo -e "\033[1;31m" "Fail." "\033[0m";
				EXIT=1;
			else
				echo -e "\033[0;32m" "Success." "\033[0m";
			fi;
		else
			echo -e "\033[1;31m" "Untested." "\033[0m";
			EXIT=2;
		fi;
	fi;
done;

exit ${EXIT}
