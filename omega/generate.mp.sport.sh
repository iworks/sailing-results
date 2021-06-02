#!/usr/bin/env bash
FILENAME=omega.sport.mistrzostwa.polski
SOURCE=${FILENAME}.csv
# sailors
OUTPUT=/tmp/${FILENAME}

echo FILENAME = ${FILENAME}
echo SOURCE   = ${SOURCE}
echo OUTPUT   = ${OUTPUT}


../_medal-stats-generator.php ${SOURCE} place-is-added hide-country > ${OUTPUT}

