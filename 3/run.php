#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function getSlope($map, $left = 3, $down = 1) {
		$x = $y = $count = 0;

		do {
			$tile = $map[$y][$x % count($map[$y])];
			if ($tile == '#') { $count++; }

			$x += $left;
			$y += $down;
		} while ($y < count($map));

		return $count;
	}

	$part1 = getSlope($map, 3, 1);
	echo 'Part 1: ', $part1, "\n";

	$part2 = getSlope($map, 1, 1) * getSlope($map, 3, 1) * getSlope($map, 5, 1) * getSlope($map, 7, 1) * getSlope($map, 1, 2);
	echo 'Part 2: ', $part2, "\n";
