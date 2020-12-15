#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = explode(',', getInputLine());


	function getSpokenNumberAt($turn, $input) {
		$i = 1;
		$spoken = [];
		$lastSpoken = 0;
		foreach ($input as $num) {
			$spoken[$num] = [$i];
			$lastSpoken = $num;

			if (isDebug()) { echo sprintf('Turn %6s', $i), ' Spoke starter number: ', $num, "\n"; }
			$i++;
		}

		while (true) {
			if (isDebug()) { echo "\t", 'Considering: ', $lastSpoken, "\n"; }

			if (count($spoken[$lastSpoken]) == 1) {
				if (isDebug()) { echo "\t", 'Only previously spoken once.', "\n"; }
				$num = 0;
			} else {
				$c = count($spoken[$lastSpoken]);
				$p1 = $spoken[$lastSpoken][$c - 1];
				$p2 = $spoken[$lastSpoken][$c - 2];

				if (isDebug()) { echo "\t", 'Previously spoken at ', $p1, ' and ', $p2, "\n"; }
				$num = abs($p1 - $p2);
			}

			if (!isset($spoken[$num])) { $spoken[$num] = []; }
			$spoken[$num][] = $i;
			$lastSpoken = $num;

			if (isDebug()) { echo sprintf('Turn %6s', $i), ' Spoke number: ', $num, "\n"; }
			$i++;

			if ($i > $turn) { return $lastSpoken; }
		}
	}

	$part1 = getSpokenNumberAt(2020, $input);
	echo 'Part 1: ', $part1, "\n";
