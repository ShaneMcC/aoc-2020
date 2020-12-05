#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');

	$part1 = 0;

	$seats = [];
	foreach (getInputLines() as $line) {
		$seatId = bindec(str_replace(['F', 'B', 'L', 'R'], ['0', '1', '0', '1'], $line));
		$part1 = max($seatId, $part1);

		$seats[] = $seatId;
	}

	sort($seats);
	$part2 = array_shift($seats);

	foreach ($seats as $s) {
		if ($part2 + 1 != $s) { $part2 = $part2 + 1; break; }
		$part2 = $s;
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
