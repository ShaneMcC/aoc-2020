#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	[$minX, $minY, $maxX, $maxY] = getBoundingBox($map);

	function step($map, $adjacentCount = 4, $adjacencyFunction = NULL) {
		if (!is_callable($adjacencyFunction)) { return $map; }

		$newMap = $map;
		foreach ($map as $y => $row) {
			foreach ($row as $x => $_) {
				if ($map[$y][$x] == '.') { continue; }

				$adjacent = 0;

				foreach (call_user_func_array($adjacencyFunction, [$map, $x, $y]) as $x2 => $y2) {
					if ($map[$y2][$x2] == '#') { $adjacent++; }
					if ($adjacent >= $adjacentCount) { $newMap[$y][$x] = 'L'; break; }
				}

				if ($adjacent == 0) { $newMap[$y][$x] = '#'; }
			}
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
		foreach ($prev as $row) {
			$acv = array_count_values($row);
			$ans += isset($acv['#']) ? $acv['#'] : 0;
		}

		return $ans;
	}

	$part1 = stepUntilNoChanges($map, 4, function($map, $x, $y) {
		foreach (yieldXY($x - 1, $y - 1, $x + 1, $y + 1) as $x2 => $y2) {
			if ($x == $x2 && $y == $y2) { continue; }
			if (!isset($map[$y2][$x2])) { continue; }

			yield $x2 => $y2;
		}
	});
	echo 'Part 1: ', $part1, "\n";

	$part2 = stepUntilNoChanges($map, 5, function($map, $x, $y) use ($minX, $minY, $maxX, $maxY) {
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
			while ($x2 >= $minX && $y2 >= $minY && $x2 <= $maxX && $y2 <= $maxY) {
				$x2 += $c[0];
				$y2 += $c[1];
				if (!isset($map[$y2][$x2])) { break; }
				if ($map[$y2][$x2] == '#' || $map[$y2][$x2] == 'L') {
					yield $x2 => $y2;
					break;
				}
			}
		}
	});
	echo 'Part 2: ', $part2, "\n";
