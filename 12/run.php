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

	$x = $y = 0;

	$changes = ['N' => [0, -1],
	            'S' => [0, +1],
	            'E' => [+1, 0],
	            'W' => [-1, 0],
	           ];

	$lastDir = 'E';
	foreach ($entries as $e) {
		$dir = $e['dir'];
		$amount = $e['amount'];

		if ($dir == 'F') {
			$dir = $lastDir;
		} else if ($dir == 'L' || $dir == 'R') {
			$amount = $amount / 90;
			while ($amount > 0) {
				if ($lastDir == 'E') {
					$lastDir = ($dir == 'L') ? 'N' : 'S';
				} else if ($lastDir == 'N') {
					$lastDir = ($dir == 'L') ? 'W' : 'E';
				} else if ($lastDir == 'W') {
					$lastDir = ($dir == 'L') ? 'S' : 'N';
				} else if ($lastDir == 'S') {
					$lastDir = ($dir == 'L') ? 'E' : 'W';
				}
				$amount--;
			}

			continue;
		}


		if (isset($changes[$dir])) {
			$x += $changes[$dir][0] * $amount;
			$y += $changes[$dir][1] * $amount;
		}
	}

	$part1 = manhattan(0, 0, $x, $y);
	echo 'Part 1: ', $part1, "\n";


	$x = $y = 0;

	$wX = 10;
	$wY = -1;

	$lastDir = 'E';
	foreach ($entries as $e) {
		$dir = $e['dir'];
		$amount = $e['amount'];
		$isWaypoint = true;

		if ($dir == 'F') {
			$x += $wX * $amount;
			$y += $wY * $amount;
			continue;
		} else if ($dir == 'N' || $dir == 'S' || $dir == 'E' || $dir == 'W') {
			$wX += $changes[$dir][0] * $amount;
			$wY += $changes[$dir][1] * $amount;
		} else if ($dir == 'L' || $dir == 'R') {
			$amount = $amount / 90;
			while ($amount > 0) {
				$oldWX = $wX;
				$oldWY = $wY;

				$isLeft = ($dir == 'L');
				if ($isLeft) {
					$wX = $oldWY;
					$wY = 0 - $oldWX;
				} else {
					$wX = 0 - $oldWY;
					$wY = $oldWX;
				}
				$amount--;
			}

			continue;
		}
	}

	$part2 = manhattan(0, 0, $x, $y);
	echo 'Part 2: ', $part2, "\n";
