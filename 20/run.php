#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$tiles = [];
	foreach ($input as $in) {
		$name = array_shift($in);
		if (preg_match('#([0-9]+)#', $name, $m)) {
			$map = [];
			foreach ($in as $i) { $map[] = str_split($i); }

			$tiles[$m[1]] = getPossibilities($map);
		}
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


	function getPossibilities($block) {
		// Find all possibilities for this block.
		$possibilities = [];

		for ($i = 0; $i < 4; $i++) {
			$rb = rotateBlock($block, $i);
			$possibilities[] = blockToString($rb);
			$possibilities[] = blockToString(flipBlock($rb));
		}

		$result = [];
		foreach (array_unique($possibilities) as $p) {
			$result[] = stringToBlock($p);
		}

		return $result;
	}

	function findEdges($tile) {
		$edges = [];
		$edges[] = implode('', $tile[0]);
		$edges[] = implode('', $tile[count($tile) - 1]);
		$edges[] = implode('', array_column($tile, 0));
		$edges[] = implode('', array_column($tile, count($tile[0]) - 1));

		return $edges;
	}

	$part1 = 1;
	$cornerTiles = [];
	$edgeTiles = [];
	$middleTiles = [];
	foreach ($tiles as $tid => $tile) {
		// echo 'TID: ', $tid, "\n";
		$edges = findEdges($tile[0]);

		$matches = 0;
		foreach ($tiles as $tid2 => $tile2) {
			if ($tid2 == $tid) { continue; }

			foreach ($tile2 as $oid2 => $orientation2) {
				$edges2 = findEdges($orientation2);
				foreach ($edges as $edge) {
					if (in_array($edge, $edges2)) {
						$matches++;
					}
				}
			}
		}

		// Divide by 4, for some reason. Who knows.
		if ($matches / 4 == 2) {
			// echo 'Corner', "\n";
			$cornerTiles[] = $tid;
		} else if ($matches / 4 == 3) {
			// echo 'Edge', "\n";
			$edgeTiles[] = $tid;
		} else if ($matches / 4 == 4) {
			// echo 'Middle', "\n";
			$middleTiles[] = $tid;
		}
	}

	echo 'Part 1: ', array_product($cornerTiles), "\n";
