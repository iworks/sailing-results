#!/usr/bin/env bash

FILENAME=$(basename "$0")
FILENAME="${FILENAME%.*}"

echo ${FILENAME}
../_medal-stats-generator.php ${FILENAME}.csv > /tmp/${FILENAME}.persons.txt
../_medal-stats-generator.php ${FILENAME}.csv country > /tmp/${FILENAME}.countries.txt
echo "cat /tmp/${FILENAME}.persons.txt /tmp/${FILENAME}.countries.txt"
