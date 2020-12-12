#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	[$minX, $minY, $maxX, $maxY] = getBoundingBox($map);

	foreach (yieldXY($minX, $minY, $maxX, $maxY) as $x => $y) {
		if ($map[$y][$x] == '.') {
			unset($map[$y][$x]);
		}
	}

	function step($map, $adjacentCount = 4, $adjacencyFunction = NULL) {
		if (!is_callable($adjacencyFunction)) { return $map; }

		$newMap = $map;
		foreach ($map as $y => $row) {
			foreach ($row as $x => $_) {
				$adjacent = 0;

				$cells = call_user_func_array($adjacencyFunction, [$map, $x, $y]);
				if (!empty($cells)) {
					foreach ($cells as $cell) {
						[$x2, $y2] = $cell;
						if ($map[$y2][$x2] == '#') { $adjacent++; }
						if ($adjacent >= $adjacentCount) { $newMap[$y][$x] = 'L'; break; }
					}
				}

				if ($adjacent == 0) { $newMap[$y][$x] = '#'; }
			}
		}

		return $newMap;
	}

	function stepUntilNoChanges($map, $adjacentCount, $adjacencyFunction) {
		$prev = $map;
		$count = 0;
		while (true) {
			$count++;
			$new = step($prev, $adjacentCount, $adjacencyFunction);
			if ($new == $prev) { break; }
			$prev = $new;
		}

		$ans = 0;
		foreach ($prev as $row) {
			$acv = array_count_values($row);
			$ans += isset($acv['#']) ? $acv['#'] : 0;
		}
		return [$count, $ans];
	}

	$part1Cache = [];
	$part1 = stepUntilNoChanges($map, 4, function($map, $x, $y) use (&$part1Cache) {
		$cellId = $y . '.' . $x;
		if (isset($part1Cache[$cellId])) {
			return $part1Cache[$cellId];
		}

		$part1Cache[$cellId] = [];
		foreach (yieldXY($x - 1, $y - 1, $x + 1, $y + 1) as $x2 => $y2) {
			if ($x == $x2 && $y == $y2) { continue; }
			if (!isset($map[$y2][$x2])) { continue; }

			$part1Cache[$cellId][] = [$x2, $y2];
		}
		return $part1Cache[$cellId];
	});
	echo 'Part 1: ', $part1[1], ' after ', $part1[0], ' steps.', "\n";

	$part2Cache = [];
	$part2 = stepUntilNoChanges($map, 5, function($map, $x, $y) use ($minX, $minY, $maxX, $maxY, &$part2Cache) {
		$cellId = $y . '.' . $x;
		if (isset($part2Cache[$cellId])) {
			return $part2Cache[$cellId];
		}
		$changes = [[-1, 0],
		            [0, -1],
		            [+1, 0],
		            [0, +1],
		            [-1, -1],
		            [-1, +1],
		            [+1, -1],
		            [+1, +1]
		           ];

		$part2Cache[$cellId] = [];
        foreach ($changes as $c) {
			$x2 = $x; $y2 = $y;
			while ($x2 >= $minX && $y2 >= $minY && $x2 <= $maxX && $y2 <= $maxY) {
				$x2 += $c[0];
				$y2 += $c[1];
				if (!isset($map[$y2][$x2])) { continue; }
				$part2Cache[$cellId][] = [$x2, $y2];
				break;
			}
		}
		return $part2Cache[$cellId];
	});

	echo 'Part 2: ', $part2[1], ' after ', $part2[0], ' steps.', "\n";
