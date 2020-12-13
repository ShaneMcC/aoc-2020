#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$timestamp = $input[0];

	$busIDs = explode(',', $input[1]);

	$min = $minID = 9999999;

	function getBusAt($busIDs, $testTime) {
		$r = [];
		foreach ($busIDs as $b) {
			if ($b == 'x') { continue; }
			if ($testTime % $b === 0) {
				$r[] = $b;
			}
		}

		return $r;
	}

	$part1 = 0;
	for ($i = $timestamp; true; $i++) {
		$b = getBusAt($busIDs, $i);
		if (!empty($b)) {
			$part1 = $b[0] * ($i - $timestamp);
			break;
		}
	}

	echo 'Part 1: ', $part1, "\n";

	$testTime = 0;
	$lastValid = 1;
	$lastValidID = -1;
	while (true) {
		if (isDebug()) {
			echo '====', "\n";
			echo $testTime, "\n";
		}
		$allTrue = true;
		foreach ($busIDs as $k => $b) {
			if ($b == 'x') { continue; }
			$c = getBusAt($busIDs, $testTime + $k);

			if (isDebug()) { echo "\t", ($testTime + $k), ' is ', json_encode($c), ' want ', $b, "\n"; }

			if (!in_array($b, $c)) {
				$allTrue = false;
				break;
			} else if ($lastValidID < $k) {
				$lastValid *= $b;
				$lastValidID = $k;
			}
		}

		if ($allTrue) {
			die('Part 2: ' . $testTime . "\n");
		}

		if (isDebug()) { echo 'Adding: ', $lastValid, "\n"; }
		$testTime += $lastValid;
	}
