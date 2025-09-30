#!/usr/bin/env bash
../_medal-stats-generator.php 5o5.world.championships.csv > /tmp/5o5.world.championships.persons.txt
../_medal-stats-generator.php 5o5.world.championships.csv country > /tmp/5o5.world.championships.countries.txt
echo "cat /tmp/5o5.world.championships.persons.txt /tmp/5o5.world.championships.countries.txt"
