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

	function getSlope($map, $left = 3, $down = 1) {
		$y = 0;
		$x = 0;
		$count = 0;

		while ($y < count($map)) {
			$x += $left;
			$y += $down;

			$tile = getTile($map, $x, $y);

			if ($tile == '#') { $count++; }
		}

		return $count;
	}

	$part1 = getSlope($map, 3, 1);
	echo 'Part 1: ', $part1, "\n";

	$part2 = getSlope($map, 1, 1) * getSlope($map, 3, 1) * getSlope($map, 5, 1) * getSlope($map, 7, 1) * getSlope($map, 1, 2);
	echo 'Part 2: ', $part2, "\n";
