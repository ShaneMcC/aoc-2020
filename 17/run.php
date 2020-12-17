#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputMap();

	$layers = [0 => [0 => $input]];

	function getPoint($layers, $x, $y, $z, $w = 0) {
		return isset($layers[$w][$z][$y][$x]) ? $layers[$w][$z][$y][$x] : '.';
	}

	function getNeighbours($x, $y, $z, $w = null) {
		$wantW = ($w !== null);
		if ($w === null) { $w = 0; }

		for ($w2 = $w - 1; $w2 <= $w + 1; $w2++) {
			for ($z2 = $z - 1; $z2 <= $z + 1; $z2++) {
				for ($y2 = $y - 1; $y2 <= $y + 1; $y2++) {
					for ($x2 = $x - 1; $x2 <= $x + 1; $x2++) {
						if (!$wantW && $x == $x2 && $y == $y2 && $z == $z2) { continue; }
						if ($wantW && $x == $x2 && $y == $y2 && $z == $z2 && $w == $w2) { continue; }

						$n = [$x2, $y2, $z2];
						if ($wantW) { $n[] = $w2; }

						$neighbours[] = $n;
					}
				}
			}

			if (!$wantW) { break; }
		}

		return $neighbours;
	}

	function step($layers, $wantW) {
		$newLayers = [];

		$minW = $wantW ? min(array_keys($layers)) - 1 : 0;
		$maxW = $wantW ? max(array_keys($layers)) + 1 : 0;

		$minZ = min(array_keys($layers[0])) - 1;
		$maxZ = max(array_keys($layers[0])) + 1;

		$minY = min(array_keys($layers[0][0])) - 1;
		$maxY = max(array_keys($layers[0][0])) + 1;

		$minX = min(array_keys($layers[0][0][0])) - 1;
		$maxX = max(array_keys($layers[0][0][0])) + 1;

		for ($w = $minW; $w <= $maxW; $w++) {
			$newLayers[$w] = [];
			for ($z = $minZ; $z <= $maxZ; $z++) {
				$newLayers[$w][$z] = [];
				for ($y = $minY; $y <= $maxY; $y++) {
					$newLayers[$w][$z][$y] = [];
					for ($x = $minX; $x <= $maxX; $x++) {
						$wasActive = getPoint($layers, $x, $y, $z, ($wantW ? $w : 0)) == '#';
						$activeNeighbours = 0;
						foreach (getNeighbours($x, $y, $z, ($wantW ? $w : null)) as $n) {
							if (getPoint($layers, $n[0], $n[1], $n[2], ($wantW ? $n[3] : 0)) == '#') {
								$activeNeighbours++;
							}
							if ($activeNeighbours > 3) { break; }
						}

						$nowActive = ($wasActive && ($activeNeighbours == 2 || $activeNeighbours == 3) || !$wasActive && $activeNeighbours == 3);
						$newLayers[$w][$z][$y][$x] = $nowActive ? '#' : '.';
					}
				}
			}
		}

		return $newLayers;
	}

	function doCycle($layers, $count, $wantW) {
		$current = $layers;
		for ($i = 0; $i < $count; $i++) {
			$current = step($current, $wantW);
		}

		$val = 0;
		foreach ($current as $w) {
			foreach ($w as $z) {
				foreach ($z as $y) {
					foreach ($y as $x) {
						if ($x == '#') {
							$val++;
						}
					}
				}
			}
		}

		return $val;
	}

	echo 'Part 1: ', doCycle($layers, 6, false), "\n";
	echo 'Part 2: ', doCycle($layers, 6, true), "\n";
