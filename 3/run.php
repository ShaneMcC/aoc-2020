#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$map = [];
	foreach ($input as $line) {
		$map[] = str_split($line);
	}

	function getTile($map, $x, $y) {
		$line = isset($map[$y]) ? $map[$y] : ['.'];

		return $line[$x % count($line)];
	}

	$y = 0;
	$x = 0;
	$part1 = 0;

	while ($y < count($map)) {
		$x += 3;
		$y += 1;

		$tile = getTile($map, $x, $y);

		if ($tile == '#') { $part1++; }
	}

	echo 'Part 1: ', $part1, "\n";
