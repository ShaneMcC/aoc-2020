#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function step($map, $adjacentCount = 4, $adjacencyFunction = NULL) {
		$newMap = $map;
		foreach (yieldXY(0, 0, count($map[0]) - 1, count($map) - 1) as $x => $y) {
			if ($map[$y][$x] == '.') { continue; }

			$adjacent = 0;
			$seatsToCheck = is_callable($adjacencyFunction) ? call_user_func_array($adjacencyFunction, [$map, $x, $y]) : [];

			foreach ($seatsToCheck as $s) {
				[$x2, $y2] = $s;
				if ($x == $x2 && $y == $y2) { continue; }
				if (!isset($map[$y2][$x2])) { continue; }

				if ($map[$y2][$x2] == '#') { $adjacent++; }
			}

			if ($adjacent == 0) { $newMap[$y][$x] = '#'; }
			if ($adjacent >= $adjacentCount) { $newMap[$y][$x] = 'L'; }
		}

		return $newMap;
	}

	function stepUntilNoChanges($map, $adjacentCount, $useVisibleSeats) {
		$prev = $map;
		while (true) {
			$new = step($prev, $adjacentCount, $useVisibleSeats);
			if ($new == $prev) { break; }
			$prev = $new;
		}

		$ans = 0;
		foreach (yieldXY(0, 0, count($prev[0]) - 1, count($prev) - 1) as $x => $y) {
			if ($prev[$y][$x] == '#') {
				$ans++;
			}
		}

		return $ans;
	}

	$part1 = stepUntilNoChanges($map, 4, function($map, $x, $y) {
		$adjacent = [];
		foreach (yieldXY($x - 1, $y - 1, $x + 1, $y + 1) as $x2 => $y2) {
			if ($x == $x2 && $y == $y2) { continue; }
			if (!isset($map[$y2][$x2])) { continue; }

			$adjacent[] = [$x2, $y2];
		}
		return $adjacent;
	});
	echo 'Part 1: ', $part1, "\n";

	$part2 = stepUntilNoChanges($map, 5, function($map, $x, $y) {
		$visible = [];

		$changes = [[-1, 0],
		            [0, -1],
		            [+1, 0],
		            [0, +1],
		            [-1, -1],
		            [-1, +1],
		            [+1, -1],
		            [+1, +1]
		           ];

        foreach ($changes as $c) {
			$x2 = $x; $y2 = $y;
			while (true) {
				$x2 += $c[0];
				$y2 += $c[1];
				if (!isset($map[$y2][$x2])) { break; }
				if ($map[$y2][$x2] != '.') {
					$visible[] = [$x2, $y2];
					break;
				}
			}
		}

		return $visible;
	});
	echo 'Part 2: ', $part2, "\n";