#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		preg_match('#([A-Z]+)([0-9]+)#SADi', $line, $m);
		[$all, $dir, $amount] = $m;
		$entries[] = ['dir' => $dir, 'amount' => $amount];
	}

	$changes = ['N' => ['d' => [0, -1], 'L' => 'W', 'R' => 'E'],
	            'S' => ['d' => [0, +1], 'L' => 'E', 'R' => 'W'],
	            'E' => ['d' => [+1, 0], 'L' => 'N', 'R' => 'S'],
	            'W' => ['d' => [-1, 0], 'L' => 'S', 'R' => 'N'],
	           ];

    function getPositionRaw($entries) {
    	global $changes;

		$x = $y = 0;
		$lastDir = 'E';
		foreach ($entries as $e) {
			$dir = $e['dir'];
			$amount = $e['amount'];

			if ($dir == 'F' || $dir == 'N' || $dir == 'S' || $dir == 'E' || $dir == 'W') {
				if ($dir == 'F') { $dir = $lastDir; }

				$x += $changes[$dir]['d'][0] * $amount;
				$y += $changes[$dir]['d'][1] * $amount;
			} else if ($dir == 'L' || $dir == 'R') {
				for ($a = $amount; $a != 0; $a -= 90) {
					$lastDir = $changes[$lastDir][$dir];
				}
			}
		}

		return [$x, $y];
	}

	function getPositionWithWaypoint($entries) {
		global $changes;

		$x = $y = 0;
		$wX = 10;
		$wY = -1;
		foreach ($entries as $e) {
			$dir = $e['dir'];
			$amount = $e['amount'];

			if ($dir == 'F') {
				$x += $wX * $amount;
				$y += $wY * $amount;
			} else if ($dir == 'N' || $dir == 'S' || $dir == 'E' || $dir == 'W') {
				$wX += $changes[$dir]['d'][0] * $amount;
				$wY += $changes[$dir]['d'][1] * $amount;
			} else if ($dir == 'L' || $dir == 'R') {
				for ($a = $amount; $a != 0; $a -= 90) {
					$oldWX = $wX;
					$oldWY = $wY;

					$wX = ($dir == 'L') ? $oldWY : 0 - $oldWY;
					$wY = ($dir == 'L') ? 0 - $oldWX : $oldWX;
				}
			}
		}

		return [$x, $y];
	}

	$part1 = getPositionRaw($entries);
	$part1 = manhattan(0, 0, $part1[0], $part1[1]);
	echo 'Part 1: ', $part1, "\n";

	$part2 = getPositionWithWaypoint($entries);
	$part2 = manhattan(0, 0, $part2[0], $part2[1]);
	echo 'Part 2: ', $part2, "\n";
