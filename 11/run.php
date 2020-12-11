#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function draw($map) {
		foreach ($map as $line) {
			echo implode('', $line), "\n";
		}
	}

	function stepPart1($map) {
		$newMap = $map;
		foreach (yieldXY(0, 0, count($map[0]) - 1, count($map) - 1) as $x => $y) {
			if ($map[$y][$x] == '.') { continue; }

			$adjacent = 0;
			foreach (yieldXY($x - 1, $y - 1, $x + 1, $y + 1) as $x2 => $y2) {
				if ($x == $x2 && $y == $y2) { continue; }
				if (!isset($map[$y2][$x2])) { continue; }

				if ($map[$y2][$x2] == '#') { $adjacent++; }
			}

			if ($adjacent == 0) { $newMap[$y][$x] = '#'; }
			if ($adjacent >= 4) { $newMap[$y][$x] = 'L'; }
		}

		return $newMap;
	}

	$prev = $map;
	$part1 = 0;
	while (true) {
		$new = stepPart1($prev);

		if ($new == $prev) { break; }
		$prev = $new;
	}

	$part1 = 0;
	foreach (yieldXY(0, 0, count($prev[0]) - 1, count($prev) - 1) as $x => $y) {
		if ($prev[$y][$x] == '#') {
			$part1++;
		}
	}

	echo 'Part 1: ', $part1, "\n";

	function getVisibleSeats($map, $x, $y) {
		$visible = [];

		$x2 = $x; $y2 = $y;
		while (true) {
			$x2--;
			if (!isset($map[$y2][$x2])) { break; }
			if ($map[$y2][$x2] != '.') {
				$visible[] = [$x2, $y2];
				break;
			}
		}

		$x2 = $x; $y2 = $y;
		while (true) {
			$x2++;
			if (!isset($map[$y2][$x2])) { break; }
			if ($map[$y2][$x2] != '.') {
				$visible[] = [$x2, $y2];
				break;
			}
		}

		$x2 = $x; $y2 = $y;
		while (true) {
			$y2--;
			if (!isset($map[$y2][$x2])) { break; }
			if ($map[$y2][$x2] != '.') {
				$visible[] = [$x2, $y2];
				break;
			}
		}

		$x2 = $x; $y2 = $y;
		while (true) {
			$y2++;
			if (!isset($map[$y2][$x2])) { break; }
			if ($map[$y2][$x2] != '.') {
				$visible[] = [$x2, $y2];
				break;
			}
		}

		$x2 = $x; $y2 = $y;
		while (true) {
			$x2--; $y2--;
			if (!isset($map[$y2][$x2])) { break; }
			if ($map[$y2][$x2] != '.') {
				$visible[] = [$x2, $y2];
				break;
			}
		}

		$x2 = $x; $y2 = $y;
		while (true) {
			$x2--; $y2++;
			if (!isset($map[$y2][$x2])) { break; }
			if ($map[$y2][$x2] != '.') {
				$visible[] = [$x2, $y2];
				break;
			}
		}

		$x2 = $x; $y2 = $y;
		while (true) {
			$x2++; $y2--;
			if (!isset($map[$y2][$x2])) { break; }
			if ($map[$y2][$x2] != '.') {
				$visible[] = [$x2, $y2];
				break;
			}
		}

		$x2 = $x; $y2 = $y;
		while (true) {
			$x2++; $y2++;
			if (!isset($map[$y2][$x2])) { break; }
			if ($map[$y2][$x2] != '.') {
				$visible[] = [$x2, $y2];
				break;
			}
		}

		return $visible;
	}

	function stepPart2($map) {
		$newMap = $map;
		foreach (yieldXY(0, 0, count($map[0]) - 1, count($map) - 1) as $x => $y) {
			if ($map[$y][$x] == '.') { continue; }

			$adjacent = 0;
			foreach (getVisibleSeats($map, $x, $y) as $s) {
				[$x2, $y2] = $s;
				if ($x == $x2 && $y == $y2) { continue; }
				if (!isset($map[$y2][$x2])) { continue; }

				if ($map[$y2][$x2] == '#') { $adjacent++; }
			}

			if ($adjacent == 0) { $newMap[$y][$x] = '#'; }
			if ($adjacent >= 5) { $newMap[$y][$x] = 'L'; }
		}

		return $newMap;
	}

	$prev = $map;
	$part2 = 0;
	while (true) {
		$new = stepPart2($prev);

		if ($new == $prev) { break; }
		$prev = $new;
	}

	$part2 = 0;
	foreach (yieldXY(0, 0, count($prev[0]) - 1, count($prev) - 1) as $x => $y) {
		if ($prev[$y][$x] == '#') {
			$part2++;
		}
	}

	echo 'Part 2: ', $part2, "\n";
