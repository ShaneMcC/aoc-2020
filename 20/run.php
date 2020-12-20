#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$tiles = [];
	// Import Tiles and all their variations.
	foreach ($input as $in) {
		$name = array_shift($in);
		if (preg_match('#([0-9]+)#', $name, $m)) {
			$map = [];
			foreach ($in as $i) { $map[] = str_split($i); }

			$tiles[$m[1]]['versions'] = getPossibilities($map);
			$tiles[$m[1]]['neighbours'] = [];
		}
	}

	// Figure out tile neighbours.
	foreach (array_keys($tiles) as $tid) {
		$tile = $tiles[$tid]['versions'][0];

		$neighbours = [];

		$edges = findEdges($tile);

		$neighbours = array_merge($neighbours, findTiles($tiles, $edges['N'], [$tid]));
		$neighbours = array_merge($neighbours, findTiles($tiles, $edges['E'], [$tid]));
		$neighbours = array_merge($neighbours, findTiles($tiles, $edges['S'], [$tid]));
		$neighbours = array_merge($neighbours, findTiles($tiles, $edges['W'], [$tid]));

		$tiles[$tid]['neighbours'] = $neighbours;
	}

	function getPossibilities($block) {
		// Find all possibilities for this block.
		$possibilities = [];

		for ($i = 0; $i < 4; $i++) {
			$r = (90 * $i);
			$rb = rotateBlock($block, $i);
			$possibilities[$r] = blockToString($rb);
			$possibilities[$r . 'f'] = blockToString(flipBlock($rb));
		}

		$result = [];
		foreach (array_unique($possibilities) as $p) {
			$result[] = stringToBlock($p);
		}

		return $result;
	}

	function rotateBlock($block, $times = 1) {
		$rotate = $block;

		for ($t = 0; $t < $times; $t++) {
			for ($i = 0; $i < count($block); $i++) {
				for ($j = 0; $j < count($block[$i]); $j++) {
					$i2 = $j;
					$j2 = count($block[$i]) - 1 - $i;

					$rotate[$i2][$j2] = $block[$i][$j];
				}
			}
			$block = $rotate;
		}

		return $rotate;
	}

	function flipBlock($block) {
		$flip = [];

		for ($i = 0; $i < count($block); $i++) {
			$flip[$i] = array_reverse($block[$i]);
		}

		return $flip;
	}

	function blockToString($block) {
		$rows = [];
		foreach ($block as $row) { $rows[] = implode('', $row); }

		return implode('/', $rows);
	}

	function stringToBlock($blockstring) {
		$block = [];
		$rows = explode('/', $blockstring);
		foreach ($rows as $row) { $block[] = str_split($row); }

		return $block;
	}

	function findEdges($tile) {
		$edges = [];
		$edges['N'] = implode('', $tile[0]);
		$edges['E'] = implode('', array_column($tile, count($tile[0]) - 1));
		$edges['S'] = implode('', $tile[count($tile) - 1]);
		$edges['W'] = implode('', array_column($tile, 0));

		return $edges;
	}

	function findTiles($tiles, $edge, $ignore = []) {
		$r = [];
		foreach ($tiles as $tid => $tile) {
			if (in_array($tid, $ignore)) { continue; }

			foreach ($tile['versions'] as $oid => $orientation) {
				$edges = findEdges($orientation);
				if (in_array($edge, $edges)) {
					$r[] = $tid;
				}
			}
		}

		return array_unique($r);
	}

	function getCornerTiles($tiles) {
		$cornerTiles = [];
		foreach ($tiles as $tid => $tile) {
			$matches = count($tile['neighbours']);

			if ($matches == 2) {
				$cornerTiles[] = $tid;
			}
		}

		return $cornerTiles;
	}

	function drawTile($tile) {
		foreach ($tile as $row) {
			echo implode('', $row), "\n";
		}
	}

	function findGrid($tiles, $startTile) {
		$size = sqrt(count($tiles));

		// Create a Grid.
		for ($y = 0; $y < $size; $y++) {
			for ($x = 0; $x < $size; $x++) {
				$grid[$y][$x] = NULL;
			}
		}

		// Add a corner, default orientation.
		$grid[0][0] = $startTile;

		if (isDebug()) {
			echo 'Looking at: 0, 0', "\n";
			echo "\t", 'Matched Tile: ', implode(', ', $grid[0][0]), "\n";
		}

		// Now, fill in the rows....
		for ($y = 0; $y < $size; $y++) {
			for ($x = 0; $x < $size; $x++) {
				if ($grid[$y][$x] != NULL) { continue; } // Already populated.
				// If we are on the first row, we are going left to right
				// If we are on any other row, we are going top to bottom.
				[$previousTileId, $prevOrientation] = ($y == 0) ? $grid[$y][$x - 1] : $grid[$y - 1][$x];

				$previousEdge = ($y == 0) ? 'E' : 'S';
				$ourEdge = ($y == 0) ? 'W' : 'N';

				if (isDebug()) {
					echo 'Looking at: ', $y, ',', $x, "\n";
					echo "\t", 'Previous Tile: ', $previousTileId, ', ', $prevOrientation, "\n";
				}

				if (!isset($tiles[$previousTileId]['versions'][$prevOrientation])) { return FALSE; }
				$previousTile = $tiles[$previousTileId]['versions'][$prevOrientation];

				$wantedEdge = findEdges($previousTile)[$previousEdge];

				if (isDebug()) {
					echo "\t", 'Previous Edge: ', $previousEdge, ' => ', $wantedEdge, "\n";
				}

				foreach ($tiles[$previousTileId]['neighbours'] as $nTileId) {
					foreach ($tiles[$nTileId]['versions'] as $oid => $t) {
						$e = findEdges($t);

						if (isDebug()) {
							echo "\t\t", 'Testing Edge: ', $nTileId, ', ', $oid, ', ', $ourEdge, ' => ', $e[$ourEdge], "\n";
						}

						if ($e[$ourEdge] == $wantedEdge) {
							$grid[$y][$x] = [$nTileId, $oid];
							break 2;
						}
					}
				}

				if ($grid[$y][$x] == null) { return FALSE; }

				if (isDebug()) {
					echo "\t", 'Matched Tile: ', implode(', ', $grid[$y][$x]), "\n";
				}
			}
		}

		return $grid;
	}

	function showGrid($grid) {
		if ($grid == FALSE) { echo '<NO GRID>'; return; }

		for ($y = 0; $y < count($grid); $y++) {
			for ($x = 0; $x < count($grid); $x++) {
				echo ' [ ', implode(', ', $grid[$y][$x]), ' ] ';
			}
			echo "\n";
		}
	}

	function findValidGrid($tiles, $startTile) {
		$grid = FALSE;

		foreach (array_keys($tiles[$startTile]['versions']) as $oid) {
			$grid = findGrid($tiles, [$startTile, $oid]);
			if ($grid != FALSE) { break; }
		}

		return $grid;
	}

	function createMap($tiles, $grid) {
		$size = sqrt(count($tiles));

		// Draw the grid...
		[$t, $o] = $grid[0][0];
		$tileSize = count($tiles[$t]['versions'][$o]);

		$map = [];

		// Grid Rows
		for ($y = 0; $y < $size; $y++) {
			// Rows within each grid without borders
			for ($tY = 1; $tY < $tileSize - 1; $tY++) {
				// Grid Columns
				$mapLine = [];
				for ($x = 0; $x < $size; $x++) {
					[$t, $o] = $grid[$y][$x];

					$row = array_splice($tiles[$t]['versions'][$o][$tY], 1, $tileSize - 2);

					$mapLine = array_merge($mapLine, $row);
				}
				$map[] = $mapLine;
			}
		}

		return $map;
	}

	function findSeaMonsters($map) {
		$smCoords = [];
		// Top Row
		$smCoords[] = [18, -1];
		// Our Row
		$smCoords[] = [0, 0];
		$smCoords[] = [5, 0];
		$smCoords[] = [6, 0];
		$smCoords[] = [11, 0];
		$smCoords[] = [12, 0];
		$smCoords[] = [17, 0];
		$smCoords[] = [18, 0];
		$smCoords[] = [19, 0];
		// Bottom Row
		$smCoords[] = [1, 1];
		$smCoords[] = [4, 1];
		$smCoords[] = [7, 1];
		$smCoords[] = [10, 1];
		$smCoords[] = [13, 1];
		$smCoords[] = [16, 1];

		$seaMonsters = 0;
		$roughness = 0;

		// We can skip top/bottom rows
		for ($y = 1; $y < count($map) - 2; $y++) {
			// We can skip if we are too near the far edge
			for ($x = 0; $x < count($map) - 20; $x++) {

				// Do we have a sea monster?
				$isSeaMonster = true;
				foreach ($smCoords as $smc) {
					if ($map[$y + $smc[1]][$x + $smc[0]] != '#') {
						$isSeaMonster = false;
						break;
					}
				}

				// Replace the sea monster so we don't miscount roughness or
				// overlapping sea monsters later.
				if ($isSeaMonster) {
					$seaMonsters++;
					foreach ($smCoords as $smc) {
						$map[$y + $smc[1]][$x + $smc[0]] = 'O';
					}
				}
			}
		}

		// Calculate Roughness.
		foreach ($map as $row) {
			$acv = array_count_values($row);
			$roughness += isset($acv['#']) ? $acv['#'] : 0;
		}

		return [$seaMonsters, $roughness, $map];
	}

	$cornerTiles = getCornerTiles($tiles);
	echo 'Part 1: ', array_product($cornerTiles), "\n";

	$grid = findValidGrid($tiles, $cornerTiles[0]);

	if (isDebug()) {
		echo 'Found grid: ', "\n";
		showGrid($grid);
	}

	$map = createMap($tiles, $grid);

	foreach (getPossibilities($map) as $pmap) {
		$fsm = findSeaMonsters($pmap);

		if ($fsm[0] > 0) {
			echo 'Part 2: Found ', $fsm[0], ' Sea Monsters - Roughness: ', $fsm[1], "\n";
		}
	}
