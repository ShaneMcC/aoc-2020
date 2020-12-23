#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function getCups($input, $totalNeeded = 0) {
		$cups = [];
		$prev = NULL;
		foreach (str_split($input) as $c) {
			$c = intval($c);
			$cups[$c] = NULL;
			if ($prev != NULL) { $cups[$prev] = $c; }
			$prev = $c;
		}

		for ($c = count($cups) + 1; $c <= $totalNeeded; $c++) {
			$cups[$c] = NULL;
			if ($prev != NULL) { $cups[$prev] = $c; }
			$prev = $c;
		}

		$cups[$prev] = array_keys($cups)[0];

		return $cups;
	}

	function moveCups($cups, $moves) {
		$isDebug = isDebug();

		$minCup = min(array_keys($cups));
		$maxCup = max(array_keys($cups));

		$currentCup = array_keys($cups)[0];

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
			$pickupEnd = $cups[$cups[$pickupStart]];

			// Remove from circle
			$cups[$currentCup] = $cups[$pickupEnd];

			// Icky.
			$pickupLabels = [$pickupStart, $cups[$pickupStart], $cups[$cups[$pickupStart]]];
			$destination = $currentCup;
			do {
				$destination -= 1;
				if ($destination < $minCup) { $destination = $maxCup; }
			} while (in_array($destination, $pickupLabels));

			if ($isDebug) {
				echo 'pick up: ', implode(', ', $pickupLabels), "\n";
				echo 'destination: ', $destination, "\n";
				echo "\n";
			}

			// Insert Cups
			$destCup = $destination;
			$afterCup = $cups[$destCup];

			// Splice into circle.
			$cups[$destCup] = $pickupStart;
			$cups[$pickupEnd] = $afterCup;

			// Move Current Cup
			$currentCup = $cups[$currentCup];
		}

		return $cups;
	}

	$part1Cups = getCups($input, 0);
	$part1Cups = moveCups($part1Cups, 100);
	$c = $part1Cups[1];
	$part1 = '';
	while ($c != '1') {
		$part1 .= $c;
		$c = $part1Cups[$c];
	}
	echo 'Part 1: ', $part1, "\n";

	$part2Cups = getCups($input, 1000000);
	$part2Cups = moveCups($part2Cups, 10000000);

	$first = $part2Cups[1];
	$second = $part2Cups[$first];
	$part2 = $first * $second;
	echo 'Part 2: ', $part2, "\n";
