#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	function getTile($directions) {
		// Hex Grids: https://www.redblobgames.com/grids/hexagons/
		$x = $y = $z = 0;

		// Pointy-Topped Hexes.
		// https://www.redblobgames.com/grids/hexagons/#coordinates-cube
		foreach ($directions as $dir) {
			switch ($dir) {
				case 'nw':
					$y++;
					$z--;
					break;
				case 'ne':
					$x++;
					$z--;
					break;
				case 'sw':
					$x--;
					$z++;
					break;
				case 'se':
					$z++;
					$y--;
					break;
				case 'w':
					$x--;
					$y++;
					break;
				case 'e':
					$x++;
					$y--;
					break;
			}
		}

		return [$x, $y, $z];
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

		[$x, $y, $z] = getTile($directions);

		$id = $z . '.' . $y . '.' . $x;
		if (!isset($map[$id])) { $map[$id] = 0; }

		echo $line, ' flips ', $id, ' from ', $map[$id], "\n";

		$map[$id] = ($map[$id] === 0) ? 1 : 0;
	}

	echo 'Part 1: ', array_count_values($map)[1], "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
