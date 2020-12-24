#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	// Hex Grids: https://www.redblobgames.com/grids/hexagons/
	// Pointy-Topped Hexes.
	// https://www.redblobgames.com/grids/hexagons/#coordinates-cube
	function getAdjacent($q, $r) {
		return ['nw' => [$q, $r - 1],
		        'ne' => [$q + 1, $r - 1],
		        'sw' => [$q - 1, $r + 1],
		        'se' => [$q, $r + 1],
		        'w' => [$q - 1, $r],
		        'e' => [$q + 1, $r],
		       ];
	}

	function generateInitialState($input) {
		$map = [];
		foreach ($input as $line) {
			$r = $q = 0;
			for ($i = 0; $i < strlen($line); $i++) {
				$move = $line[$i];
				if ($move == 's' || $move == 'n') { $move .= $line[++$i]; }

				[$q, $r] = getAdjacent($q, $r)[$move];
			}

			if (isset($map[$r][$q])) {
				unset($map[$r][$q]);
				if (empty($map[$r])) { unset($map[$r]); }
			} else {
				if (!isset($map[$r])) { $map[$r] = []; }
				$map[$r][$q] = 1;
			}
		}

		return $map;
	}

	function countBlackTiles($map) {
		$count = 0;
		foreach ($map as $r => $qR) {
			$acv = array_count_values($qR);
			$count += isset($acv[1]) ? $acv[1] : 0;
		}
		return $count;
	}

	function step($map) {
		$newMap = $map;

		[$minQ, $minR, $maxQ, $maxR] = getBoundingBox($map);

		for ($r = $minR - 2; $r < $maxR + 2; $r++) {
			for ($q = $minQ - 2; $q < $maxQ + 2; $q++) {

				$adjacent = getAdjacent($q, $r);

				$ac = 0;
				foreach ($adjacent as $t) {
					[$tQ, $tR] = $t;

					if (isset($map[$tR][$tQ]) && $map[$tR][$tQ] === 1) {
						$ac++;
						if ($ac > 2) { break; }
					}
				}

				if (isset($map[$r][$q]) && $map[$r][$q] == 1) {
					if ($ac == 0 || $ac > 2) {
						unset($newMap[$r][$q]);
					}
				} else {
					if ($ac == 2) {
						$newMap[$r][$q] = 1;
					}
				}
			}
		}

		return $newMap;
	}

	$map = generateInitialState($input);
	$part1 = countBlackTiles($map);
	echo 'Part 1: ', $part1, "\n";

	for ($day = 1; $day <= 100; $day++) {
		$map = step($map);
	}

	$part2 = countBlackTiles($map);
	echo 'Part 2: ', $part2, "\n";
