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

	// LCM and GCD from https://stackoverflow.com/questions/147515/least-common-multiple-for-3-or-more-numbers
	function gcd($a, $b) {
		$t = 0;
		while ($b != 0){
			$t = $b;
			$b = $a % $b;
			$a = $t;
		}

		return $a;
	}

	function lcm($a, $b) {
		return ($a * $b / gcd($a, $b));
	}

	function hasSequentialBusses($busIDs, $val) {
		$incrementer = 1;

		foreach ($busIDs as $i => $b) {
			if (($val + $i) % $b !== 0) {
				return [false, $incrementer];
			} else {
				$incrementer = lcm($incrementer, $b);
			}
		}

		return [true];
	}

	function getEarliestSequentialTime($busIDs) {
		$testTime = 0;
		while (true) {
			foreach ($busIDs as $k => $b) {
				$c = hasSequentialBusses($busIDs, $testTime);

				if ($c[0]) {
					return $testTime;
				} else {
					$testTime += $c[1];
				}
			}
		}
	}

	$part2 = getEarliestSequentialTime($busIDs);
	echo 'Part 2: ', $part2, "\n";
