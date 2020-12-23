#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	$cups = str_split($input);

	function moveCups($cups, $moves) {
		$isDebug = isDebug();

		$currentIndex = 0;

		$minCup = min($cups);
		$maxCup = max($cups);

		for ($move = 1; $move <= $moves; $move++) {
			// Current Cup Label
			$currentLabel = $cups[$currentIndex];
			if ($isDebug) {
				echo '-- move ', $move, ' --', "\n";
				echo 'cups:';
				for ($c = 0; $c < count($cups); $c++) {
					echo ($c == $currentIndex) ? ' (' : '  ';
					echo $cups[$c];
					echo ($c == $currentIndex) ? ')' : '';
				}
				echo "\n";
			};

			$pickup = array_splice($cups, $currentIndex + 1, 3, []);
			if (count($pickup) < 3) {
				$pickup = array_merge($pickup, array_splice($cups, 0, 3 - count($pickup), []));
			}

			$destination = $currentLabel;
			do {
				$destination -= 1;
				if ($destination < $minCup) { $destination = $maxCup; }
			} while (in_array($destination, $pickup));

			if ($isDebug) {
				echo 'pick up: ', implode(', ', $pickup), "\n";
				echo 'destination: ', $destination, "\n";
				echo "\n";
			}

			// Insert Cups
			$index = array_search($destination, $cups);
			array_splice($cups, $index, 1, array_merge([$destination], $pickup));

			// Move Current Cup
			$currentIndex = (array_search($currentLabel, $cups) + 1) % count($cups);
		}

		return $cups;
	}

	function getCupsAsAnswer($cups) {
		$end = array_splice($cups, array_search('1', $cups));
		$final = array_merge($end, $cups);
		array_shift($final);

		return implode('', $final);
	}

	$part1 = moveCups($cups, 100);
	$part1 = getCupsAsAnswer($part1);

	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
