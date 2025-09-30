#!/usr/bin/env bash
../_medal-stats-generator.php 5o5.european.championships.csv > /tmp/5o5.european.championships.persons.txt
../_medal-stats-generator.php 5o5.european.championships.csv country > /tmp/5o5.european.championships.countries.txt
echo "cat /tmp/5o5.european.championships.persons.txt /tmp/5o5.european.championships.countries.txt"
