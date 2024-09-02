#!/usr/bin/env bash
FILENAME=moth.pl.international.championships
SOURCE=${FILENAME}.csv
# sailors
OUTPUT=/tmp/${FILENAME}

echo FILENAME = ${FILENAME}
echo SOURCE   = ${SOURCE}
echo OUTPUT   = ${OUTPUT}


../_medal-stats-generator.php ${SOURCE} hide-country > ${OUTPUT}

