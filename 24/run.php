#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	function getTile($directions) {
		// Hex Grids: https://www.redblobgames.com/grids/hexagons/
		$r = $q = 0;

		// Pointy-Topped Hexes.
		// https://www.redblobgames.com/grids/hexagons/#coordinates-cube
		foreach ($directions as $dir) {
			switch ($dir) {
				case 'nw':
					$r--;
					break;
				case 'ne':
					$q++;
					$r--;
					break;
				case 'sw':
					$q--;
					$r++;
					break;
				case 'se':
					$r++;
					break;
				case 'w':
					$q--;
					break;
				case 'e':
					$q++;
					break;
			}
		}

		return [$r, $q];
	}

	$map = [];
	foreach ($input as $line) {
		$directions = [];
		for ($i = 0; $i < strlen($line); $i++) {
			$move = $line[$i];
			if ($move == 's' || $move == 'n') {
				$move .= $line[++$i];
			}
			$directions[] = $move;
		}

		[$r, $q] = getTile($directions);

		if (!isset($map[$r])) { $map[$r] = []; }
		if (!isset($map[$r][$q])) { $map[$r][$q] = 0; }

		$map[$r][$q] = ($map[$r][$q] === 0) ? 1 : 0;
	}

	$part1 = 0;
	foreach ($map as $r => $qR) {
		foreach ($qR as $q => $c) {
			if ($c === 1) { $part1++; }
		}
	}

	echo 'Part 1: ', $part1, "\n";

	for ($day = 1; $day <= 100; $day++) {
		$newMap = $map;

		[$minQ, $minR, $maxQ, $maxR] = getBoundingBox($map);

		for ($r = $minR - 2; $r < $maxR + 2; $r++) {
			for ($q = $minQ - 2; $q < $maxQ + 2; $q++) {

				$adjacent = [[$r - 1, $q],
				             [$r - 1, $q + 1],
				             [$r + 1, $q - 1],
				             [$r + 1, $q],
				             [$r, $q - 1],
				             [$r, $q + 1],
				            ];

				$ac = 0;
				foreach ($adjacent as $t) {
					[$tR, $tQ] = $t;

					if (isset($map[$tR][$tQ]) && $map[$tR][$tQ] === 1) {
						$ac++;
					}
				}

				if (isset($map[$r][$q]) && $map[$r][$q] == 1) {
					if ($ac == 0 || $ac > 2) {
						$newMap[$r][$q] = 0;
					}
				} else {
					if ($ac == 2) {
						$newMap[$r][$q] = 1;
					}
				}
			}
		}
		$map = $newMap;
	}

	$part2 = 0;
	foreach ($newMap as $r => $qR) {
		foreach ($qR as $q => $c) {
			if ($c === 1) { $part2++; }
		}
	}
	echo 'Part 2: ', $part2, "\n";
