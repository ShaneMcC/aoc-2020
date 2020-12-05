#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$part1 = 0;

	$entries = [];
	foreach ($input as $line) {
		$bits = str_split($line);

		$RowMin = 0;
		$RowMax = 128;

		$ColMin = 0;
		$ColMax = 8;

		foreach ($bits as $bit) {
			if ($bit == 'F') {
				$RowMax -= ($RowMax - $RowMin) / 2;
			} else if ($bit == 'B') {
				$RowMin += ($RowMax - $RowMin) / 2;
			} else if ($bit == 'L') {
				$ColMax -= ($ColMax - $ColMin) / 2;
			} else if ($bit == 'R') {
				$ColMin += ($ColMax - $ColMin) / 2;
			}
		}

		$seatId = $RowMin * 8 + $ColMin;

		$entries[$seatId] = ['data' => $line, 'row' => $RowMin, 'col' => $ColMin, 'seatId' => $seatId];

		$part1 = max($seatId, $part1);
	}

	$seats = array_keys($entries);
	sort($seats);

	$part2 = 0;
	foreach ($seats as $s) {
		if ($part2 == 0) { $part2 = $s; continue; }
		if ($part2 + 1 != $s) { $part2 = $part2 + 1; break; }
		$part2 = $s;
	}


	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
