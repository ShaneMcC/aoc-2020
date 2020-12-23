#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	class Cup {
		public $val = 0;

		public $next = null;
		public $prev = null;

		public function __construct($val) {
			$this->val = $val;
		}
	}

	function getCups($input, $totalNeeded = 0) {
		$first = NULL;
		$prev = NULL;

		$cups = [];

		foreach (str_split($input) as $c) {
			$cup = new Cup($c);
			$cups[$c] = $cup;

			$cup->prev = $prev;
			if ($prev == null) { $first = $cup; }
			else { $prev->next = $cup; }
			$prev = $cup;
		}

		for ($i = count($cups) + 1; $i <= $totalNeeded; $i++) {
			$cup = new Cup($i);
			$cups[$i] = $cup;

			$cup->prev = $prev;
			if ($prev == null) { $first = $cup; }
			else { $prev->next = $cup; }
			$prev = $cup;
		}

		$first->prev = $prev;
		$prev->next = $first;

		return $cups;
	}

	function moveCups($cups, $moves) {
		$isDebug = isDebug();

		$minCup = min(array_keys($cups));
		$maxCup = max(array_keys($cups));

		$currentCup = $cups[array_keys($cups)[0]];

		for ($move = 1; $move <= $moves; $move++) {
			if ($isDebug) {
				echo '-- move ', $move, ' --', "\n";
				echo 'cups:';
				$indexCup = $currentCup->prev->prev;
				for ($c = 0; $c < 14; $c++) {
					echo ($indexCup == $currentCup) ? ' (' : '  ';
					echo $indexCup->val;
					echo ($indexCup == $currentCup) ? ')' : ' ';
					$indexCup = $indexCup->next;
				}
				echo "\n";
			};

			// Pickup
			$pickupStart = $currentCup->next;
			$pickupEnd = $pickupStart->next->next;

			// Remove from circle
			$currentCup->next = $pickupEnd->next;
			$pickupEnd->prev = $currentCup;


			// Icky.
			$pickupLabels = [$pickupStart->val, $pickupStart->next->val, $pickupStart->next->next->val];
			$destination = $currentCup->val;
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
			$destCup = $cups[$destination];
			$afterCup = $destCup->next;

			// Splice into circle.
			$destCup->next = $pickupStart;
			$pickupStart->prev = $destCup;
			$pickupEnd->next = $afterCup;
			$afterCup->prev = $pickupEnd;

			// Move Current Cup
			$currentCup = $currentCup->next;
		}

		return $cups;
	}

	$part1Cups = getCups($input, 0);
	$part1Cups = moveCups($part1Cups, 100);
	$c = $part1Cups[1]->next;
	$part1 = '';
	while ($c->val != '1') {
		$part1 .= $c->val;
		$c = $c->next;
	}
	echo 'Part 1: ', $part1, "\n";

	$part2Cups = getCups($input, 1000000);
	$part2Cups = moveCups($part2Cups, 10000000);

	$first = $part2Cups[1]->next;
	$second = $part2Cups[1]->next->next;
	$part2 = $first->val * $second->val;
	echo 'Part 2: ', $part2, "\n";
