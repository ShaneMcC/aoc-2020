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

			$tiles[$m[1]]['orientations'] = getOrientations($map);
			$tiles[$m[1]]['neighbours'] = [];
		}
	}

	// Figure out tile neighbours.
	foreach (array_keys($tiles) as $tid) {
		$tile = $tiles[$tid]['orientations'][0];

		$edges = getEdges($tile);

		foreach ($edges as $edge) {
			$n = findTileWithEdge($tiles, $edge, $tid);
			if ($n !== FALSE) {
				$tiles[$tid]['neighbours'][] = $n;
			}
		}
	}

	// Find all possibilities for this block.
	function getOrientations($block) {
		$result = [];

		for ($i = 0; $i < 4; $i++) {
			$r = (90 * $i);
			$result[$r] = $block;
			$result[$r . 'f'] = flipBlock($block);
			$block = rotateBlock($block);
		}

		return $result;
	}

	function rotateBlock($block) {
		$rotate = $block;

		for ($i = 0; $i < count($block); $i++) {
			for ($j = 0; $j < count($block[$i]); $j++) {
				$i2 = $j;
				$j2 = count($block[$i]) - 1 - $i;

				$rotate[$i2][$j2] = $block[$i][$j];
			}
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

	function getEdges($tile) {
		$edges = [];
		$edges['N'] = implode('', $tile[0]);
		$edges['E'] = implode('', array_column($tile, count($tile[0]) - 1));
		$edges['S'] = implode('', $tile[count($tile) - 1]);
		$edges['W'] = implode('', array_column($tile, 0));

		return $edges;
	}

	function findTileWithEdge($tiles, $edge, $exclude = '') {
		$r = [];
		foreach ($tiles as $tid => $tile) {
			if ($tid == $exclude) { continue; }

			foreach ($tile['orientations'] as $oid => $orientation) {
				$edges = getEdges($orientation);
				if (in_array($edge, $edges)) {
					return $tid;
				}
			}
		}

		return FALSE;
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

	function findGrid($tiles, $startTile) {
		$size = sqrt(count($tiles));

		// Create a Grid.
		for ($y = 0; $y < $size; $y++) {
			for ($x = 0; $x < $size; $x++) {
				$grid[$y][$x] = NULL;
			}
		}

		// Add our starting corner piece
		$grid[0][0] = $startTile;

		$isDebug = isDebug();

		if ($isDebug) {
			echo 'Looking at: 0, 0', "\n";
			echo "\t", 'Matched Tile: ', implode(', ', $grid[0][0]), "\n";
		}

		// Now, fill in the rows....
		// Top row we go left to right matching along the East/West border
		// Then for every subsequent row, we look at the tile above us and go north/south
		for ($y = 0; $y < $size; $y++) {
			for ($x = 0; $x < $size; $x++) {
				if ($grid[$y][$x] != NULL) { continue; } // Already populated.
				// If we are on the first row, we are going left to right
				// If we are on any other row, we are going top to bottom.
				[$previousTileId, $prevOrientation] = ($y == 0) ? $grid[$y][$x - 1] : $grid[$y - 1][$x];
				[$previousEdge, $ourEdge] = ($y == 0) ? ['E', 'W'] : ['S', 'N'];

				if ($isDebug) {
					echo 'Looking at: ', $y, ',', $x, "\n";
					echo "\t", 'Previous Tile: ', $previousTileId, ', ', $prevOrientation, "\n";
				}

				if (!isset($tiles[$previousTileId]['orientations'][$prevOrientation])) { return FALSE; }
				$previousTile = $tiles[$previousTileId]['orientations'][$prevOrientation];

				$wantedEdge = getEdges($previousTile)[$previousEdge];

				if ($isDebug) {
					echo "\t", 'Previous Edge: ', $previousEdge, ' => ', $wantedEdge, "\n";
				}

				// Look at all our neighbours
				foreach ($tiles[$previousTileId]['neighbours'] as $nTileId) {
					// And check each orientation.
					foreach ($tiles[$nTileId]['orientations'] as $oid => $t) {
						$e = getEdges($t);

						if ($isDebug) {
							echo "\t\t", 'Testing Edge: ', $nTileId, ', ', $oid, ', ', $ourEdge, ' => ', $e[$ourEdge], "\n";
						}

						if ($e[$ourEdge] == $wantedEdge) {
							$grid[$y][$x] = [$nTileId, $oid];
							break 2;
						}
					}
				}

				if ($grid[$y][$x] == null) { return FALSE; }

				if ($isDebug) {
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

		foreach (array_keys($tiles[$startTile]['orientations']) as $oid) {
			$grid = findGrid($tiles, [$startTile, $oid]);
			if ($grid != FALSE) { break; }
		}

		return $grid;
	}

	function createMap($tiles, $grid) {
		$size = sqrt(count($tiles));

		// Draw the grid...
		[$t, $o] = $grid[0][0];
		$tileSize = count($tiles[$t]['orientations'][$o]);

		$map = [];

		// Grid Rows
		for ($y = 0; $y < $size; $y++) {
			// Rows within each grid without borders
			for ($tY = 1; $tY < $tileSize - 1; $tY++) {
				// Grid Columns
				$mapLine = [];
				for ($x = 0; $x < $size; $x++) {
					[$t, $o] = $grid[$y][$x];

					$row = array_splice($tiles[$t]['orientations'][$o][$tY], 1, $tileSize - 2);

					$mapLine += array_merge($mapLine, $row);
				}
				$map[] = $mapLine;
			}
		}

		return $map;
	}

	function getSeaMonster() {
		$smCoords = [];
		// Top Row
		$smCoords[] = [18, 0];
		// Our Row
		$smCoords[] = [0, 1];
		$smCoords[] = [5, 1];
		$smCoords[] = [6, 1];
		$smCoords[] = [11, 1];
		$smCoords[] = [12, 1];
		$smCoords[] = [17, 1];
		$smCoords[] = [18, 1];
		$smCoords[] = [19, 1];
		// Bottom Row
		$smCoords[] = [1, 2];
		$smCoords[] = [4, 2];
		$smCoords[] = [7, 2];
		$smCoords[] = [10, 2];
		$smCoords[] = [13, 2];
		$smCoords[] = [16, 2];

		return $smCoords;
	}

	function findSeaMonsters($map, $seamonster) {
		$seaMonsters = 0;
		$roughness = 0;

		$smWidth = max(array_column($seamonster, 0));
		$smHeight = max(array_column($seamonster, 1));

		// We can skip bottom 2 rows because we start checking out
		for ($y = 0; $y < count($map) - $smHeight; $y++) {
			// We can skip if we are too near the far edge
			for ($x = 0; $x < count($map) - $smWidth; $x++) {

				// Do we have a sea monster?
				$isSeaMonster = true;
				foreach ($seamonster as $sm) {
					if ($map[$y + $sm[1]][$x + $sm[0]] != '#') {
						$isSeaMonster = false;
						break;
					}
				}

				// Replace the sea monster so we don't miscount roughness or
				// overlapping sea monsters later.
				if ($isSeaMonster) {
					$seaMonsters++;
					foreach ($seamonster as $sm) {
						$map[$y + $sm[1]][$x + $sm[0]] = 'O';
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
	if (count($cornerTiles) != 4) { die('Unable to find 4 corners.'."\n"); }

	echo 'Part 1: ', array_product($cornerTiles), "\n";

	$grid = findValidGrid($tiles, $cornerTiles[0]);
	if ($grid == FALSE) { die('Unable to find valid grid layout.'."\n"); }

	if (isDebug()) {
		echo 'Found grid: ', "\n";
		showGrid($grid);
	}

	$map = createMap($tiles, $grid);
	$monster = getSeaMonster();

	foreach (getOrientations($map) as $pmap) {
		$fsm = findSeaMonsters($pmap, $monster);

		if ($fsm[0] > 0) {
			echo 'Part 2: Found ', $fsm[0], ' Sea Monsters - Roughness: ', $fsm[1], "\n";
			break;
		}
	}
