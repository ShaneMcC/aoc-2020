#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$timestamp = $input[0];

	$busIDs = [];

	$min = $minID = 9999999;

	$i = 0;
	foreach (explode(',', $input[1]) as $b) {
		if ($b == 'x') {
			$c = 0;
		} else {
			$c = $b;
			do {
				$c += $b;
			} while ($c < $timestamp);

			if ($c < $min) {
				$min = $c;
				$minID = $b;
			}
		}

		$busIDs[$i] = [$b, $c];
		$i++;
	}

	$part1 = $minID * ($min - $timestamp);
	echo 'Part 1: ', $part1, "\n";
