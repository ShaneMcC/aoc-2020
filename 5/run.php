#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');

	$part1 = 0;

	$seats = [];
	foreach (getInputLines() as $line) {
		$seatId = bindec(str_replace(['F', 'B', 'L', 'R'], ['0', '1', '0', '1'], $line));
		$part1 = max($seatId, $part1);

		$seats[$seatId] = true;
	}

	foreach ($seats as $s => $_) {
		if ($s != $part1 && !isset($seats[$s + 1])) {
			$part2 = $s + 1;
			break;
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
