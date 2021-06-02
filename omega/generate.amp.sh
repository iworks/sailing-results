#!/usr/bin/env bash
FILENAME=omega.standard.akademickie.mistrzostwa.polski
SOURCE=${FILENAME}.csv
#
# sailors
#
OUTPUT=/tmp/${FILENAME}

echo FILENAME = ${FILENAME}
echo SOURCE   = ${SOURCE}
echo OUTPUT   = ${OUTPUT}
echo

../_medal-stats-generator.php ${SOURCE} place-is-added hide-country no-country > ${OUTPUT}

#
# universites
#
OUTPUT=/tmp/${FILENAME}-uczelnie
echo FILENAME = ${FILENAME}
echo SOURCE   = ${SOURCE}
echo OUTPUT   = ${OUTPUT}
echo

../_medal-stats-generator.php ${SOURCE} place-is-added hide-country no-country universities > ${OUTPUT}

