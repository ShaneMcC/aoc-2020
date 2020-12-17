#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputMap();

	$layers = [0 => $input];
	$layersW = [0 => [0 => $input]];

	function getPoint($layers, $x, $y, $z) {
		return isset($layers[$z][$y][$x]) ? $layers[$z][$y][$x] : '.';
	}

	function getPointW($layers, $x, $y, $z, $w) {
		return isset($layers[$w][$z][$y][$x]) ? $layers[$w][$z][$y][$x] : '.';
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

	function getNeighboursW($x, $y, $z, $w) {
		for ($w2 = $w - 1; $w2 <= $w + 1; $w2++) {
			for ($z2 = $z - 1; $z2 <= $z + 1; $z2++) {
				for ($y2 = $y - 1; $y2 <= $y + 1; $y2++) {
					for ($x2 = $x - 1; $x2 <= $x + 1; $x2++) {
						if ($x == $x2 && $y == $y2 && $z == $z2 && $w == $w2 ) { continue; }

						$neighbours[] = [$x2, $y2, $z2, $w2];
					}
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

	function stepW($layers) {
		$newLayers = [];

		$minW = min(array_keys($layers));
		$maxW = max(array_keys($layers));

		$minZ = min(array_keys($layers[0]));
		$maxZ = max(array_keys($layers[0]));

		$minY = min(array_keys($layers[0][0]));
		$maxY = max(array_keys($layers[0][0]));

		$minX = min(array_keys($layers[0][0][0]));
		$maxX = max(array_keys($layers[0][0][0]));;

		for ($w = $minW - 1; $w <= $maxW + 1; $w++) {
			$newLayers[$w] = [];
			for ($z = $minZ - 1; $z <= $maxZ + 1; $z++) {
				$newLayers[$w][$z] = [];
				for ($y = $minY - 1; $y <= $maxY + 1; $y++) {
					$newLayers[$w][$z][$y] = [];
					for ($x = $minX - 1; $x <= $maxX + 1; $x++) {
						$wasActive = getPointW($layers, $x, $y, $z, $w) == '#';
						$activeNeighbours = 0;
						foreach (getNeighboursW($x, $y, $z, $w) as $n) {
							if (getPointW($layers, $n[0], $n[1], $n[2], $n[3]) == '#') {
								$activeNeighbours++;
							}
							if ($activeNeighbours > 3) { break; }
						}

						$newLayers[$w][$z][$y][$x] = ($wasActive && ($activeNeighbours == 2 || $activeNeighbours == 3) || !$wasActive && $activeNeighbours == 3) ? '#' : '.';
					}
				}
			}
		}

		return $newLayers;
	}

	function doCycle($layers, $count) {
		$current = $layers;
		for ($i = 0; $i < $count; $i++) {
			$current = step($current);
		}

		$val = 0;
		foreach ($current as $z) {
			foreach ($z as $y) {
				foreach ($y as $x) {
					if ($x == '#') {
						$val++;
					}
				}
			}
		}

		return $val;
	}

	function doCycleW($layers, $count) {
		$current = $layers;
		for ($i = 0; $i < $count; $i++) {
			$current = stepW($current);
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

	echo 'Part 1: ', doCycle($layers, 6), "\n";
	echo 'Part 2: ', doCycleW($layersW, 6), "\n";
