#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function getCups($input, $totalNeeded = 0) {
		$cups = [];
		$prev = NULL;

		for ($i = 1; $i <= max(strlen($input), $totalNeeded); $i++) {
			$c = ($i <= strlen($input)) ? intval($input[$i - 1]) : $i;

			$cups[$c] = NULL;
			if ($prev != NULL) { $cups[$prev] = $c; }
			$prev = $c;
		}

		$cups[$prev] = array_keys($cups)[0];

		return $cups;
	}

	function moveCups($cups, $moves) {
		$isDebug = isDebug();

		$keys = array_keys($cups);
		$minCup = min($keys);
		$maxCup = max($keys);
		$currentCup = $keys[0];

		for ($move = 1; $move <= $moves; $move++) {
			if ($isDebug) {
				echo '-- move ', $move, ' --', "\n";
				echo 'cups:';
				$indexCup = $currentCup;
				for ($c = 0; $c < min(count($cups), 14); $c++) {
					echo ($indexCup == $currentCup) ? ' (' : '  ';
					echo $indexCup;
					echo ($indexCup == $currentCup) ? ')' : ' ';
					$indexCup = $cups[$indexCup];
				}
				echo "\n";
			};

			// Pickup
			$pickupStart = $cups[$currentCup];
			$pickupMid = $cups[$pickupStart];
			$pickupEnd = $cups[$pickupMid];

			// Remove from circle
			$cups[$currentCup] = $cups[$pickupEnd];

			// Find Destination
			$pickupLabels = [$pickupStart, $pickupMid, $pickupEnd];
			$destination = $currentCup;
			do {
				$destination -= 1;
				if ($destination < $minCup) { $destination = $maxCup; }
			} while (in_array($destination, $pickupLabels));

			// Insert Cups
			$destCup = $destination;
			$afterCup = $cups[$destCup];

			// Splice into circle.
			$cups[$destCup] = $pickupStart;
			$cups[$pickupEnd] = $afterCup;

			// Move Current Cup
			$currentCup = $cups[$currentCup];

			if ($isDebug) {
				echo 'pick up: ', implode(', ', $pickupLabels), "\n";
				echo 'destination: ', $destination, "\n";
				echo "\n";
			}
		}

		return $cups;
	}

	$part1Cups = moveCups(getCups($input, 0), 100);
	$part1 = '';
	$c = $part1Cups[1];
	while ($c != '1') {
		$part1 .= $c;
		$c = $part1Cups[$c];
	}
	echo 'Part 1: ', $part1, "\n";

	$part2Cups = moveCups(getCups($input, 1000000), 10000000);
	$part2 = $part2Cups[1] * $part2Cups[$part2Cups[1]];
	echo 'Part 2: ', $part2, "\n";
