#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function getSlope($map, $left = 3, $down = 1) {
		$x = $y = $count = 0;

		$width = count($map[0]);
		do {
			if ($map[$y][$x % $width] == '#') { $count++; }

			$x += $left;
			$y += $down;
		} while ($y < count($map));

		return $count;
	}

	$part1 = getSlope($map, 3, 1);
	echo 'Part 1: ', $part1, "\n";

	$part2 = [];
	$part2[] = getSlope($map, 1, 1);
	$part2[] = getSlope($map, 3, 1);
	$part2[] = getSlope($map, 5, 1);
	$part2[] = getSlope($map, 7, 1);
	$part2[] = getSlope($map, 1, 2);

	echo 'Part 2: ', implode(' * ', $part2), ' = ', array_product($part2), "\n";
