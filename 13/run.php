#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$timestamp = $input[0];

	$busIDs = [];
	foreach (explode(',', $input[1]) as $i => $b) {
		if ($b != 'x') {
			$busIDs[$i] = $b;
		}
	}

	function getEarliestBusTime($busIDs, $timestamp) {
		for ($i = $timestamp; true; $i++) {
			foreach ($busIDs as $b) {
				if ($i % $b === 0) {
					return $b * ($i - $timestamp);;
				}
			}
		}
	}

	$part1 = getEarliestBusTime($busIDs, $timestamp);
	echo 'Part 1: ', $part1, "\n";

	function hasSequentialBusses($busIDs, $val) {
		$lastGood = [false, 0, 1];

		foreach ($busIDs as $i => $b) {
			if (($val + $i) % $b !== 0) {
				return $lastGood;
			} else {
				$lastGood = [false, $i, $b];
			}
		}

		return [true];
	}

	$testTime = 0;
	$lastValid = 1;
	$lastValidID = -1;
	while (true) {
		foreach ($busIDs as $k => $b) {
			$c = hasSequentialBusses($busIDs, $testTime);

			if ($c[0]) {
				die('Part 2: ' . $testTime . "\n");
			} else if ($lastValidID < $c[1]) {
				$lastValid *= $c[2];
				$lastValidID = $c[1];
			}
		}

		$testTime += $lastValid;
	}
