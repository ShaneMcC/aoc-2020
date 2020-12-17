#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputMap();

	$layers = [0 => $input];

	function getPoint($layers, $x, $y, $z) {
		return isset($layers[$z][$y][$x]) ? $layers[$z][$y][$x] : '.';
	}

	function getNeighbours($x, $y, $z) {
		for ($z2 = $z - 1; $z2 <= $z + 1; $z2++) {
			for ($y2 = $y - 1; $y2 <= $y + 1; $y2++) {
				for ($x2 = $x - 1; $x2 <= $x + 1; $x2++) {
					if ($x == $x2 && $y == $y2 && $z == $z2 ) { continue; }

					$neighbours[] = [$x2, $y2, $z2];
				}
			}
		}

		return $neighbours;
	}

	function step($layers) {
		$newLayers = [];

		$minZ = min(array_keys($layers));
		$maxZ = max(array_keys($layers));

		$minY = min(array_keys($layers[0]));
		$maxY = max(array_keys($layers[0]));

		$minX = min(array_keys($layers[0][0]));
		$maxX = max(array_keys($layers[0][0]));;

		for ($z = $minZ - 1; $z <= $maxZ + 1; $z++) {
			$newLayers[$z] = [];
			for ($y = $minY - 1; $y <= $maxY + 1; $y++) {
				$newLayers[$z][$y] = [];
				for ($x = $minX - 1; $x <= $maxX + 1; $x++) {
					$wasActive = getPoint($layers, $x, $y, $z) == '#';
					$activeNeighbours = 0;
					foreach (getNeighbours($x, $y, $z) as $n) {
						if (getPoint($layers, $n[0], $n[1], $n[2]) == '#') {
							$activeNeighbours++;
						}
						if ($activeNeighbours > 3) { break; }
					}

					$newLayers[$z][$y][$x] = ($wasActive && ($activeNeighbours == 2 || $activeNeighbours == 3) || !$wasActive && $activeNeighbours == 3) ? '#' : '.';
				}
			}
		}

		return $newLayers;
	}

	$current = $layers;
	for ($i = 0; $i < 6; $i++) {
		$current = step($current);
	}

	$part1 = 0;
	foreach ($current as $z) {
		foreach ($z as $y) {
			foreach ($y as $x) {
				if ($x == '#') {
					$part1++;
				}
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
