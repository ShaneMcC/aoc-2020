#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');

	$part1 = 0;

	$seats = [];
	foreach (getInputLines() as $line) {
		$minCol = $minRow = 0;
		$maxRow = 128;
		$maxCol = 8;

		foreach (str_split($line) as $bit) {
			if ($bit == 'F') {
				$maxRow -= ($maxRow - $minRow) / 2;
			} else if ($bit == 'B') {
				$minRow += ($maxRow - $minRow) / 2;
			} else if ($bit == 'L') {
				$maxCol -= ($maxCol - $minCol) / 2;
			} else if ($bit == 'R') {
				$minCol += ($maxCol - $minCol) / 2;
			}
		}

		$seatId = ($minRow * 8) + $minCol;
		$seats[] = $seatId;
		$part1 = max($seatId, $part1);
	}
	sort($seats);

	$part2 = array_shift($seats);

	foreach ($seats as $s) {
		if ($part2 + 1 != $s) { $part2 = $part2 + 1; break; }
		$part2 = $s;
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
