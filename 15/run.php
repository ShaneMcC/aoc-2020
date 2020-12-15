#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = explode(',', getInputLine());

	function getSpokenNumberAt($turn, $input) {
		$i = 1;
		$spoken = [];
		$lastSpoken = 0;
		foreach ($input as $num) {
			$spoken[$num] = [$i, $i];
			$lastSpoken = $num;

			if (isDebug()) { echo sprintf('Turn %6s', $i), ' Spoke starter number: ', $num, "\n"; }
			$i++;
		}

		while (true) {
			if (isDebug()) { echo "\t", 'Considering: ', $lastSpoken, "\n"; }

			$p1 = $spoken[$lastSpoken][0];
			$p2 = $spoken[$lastSpoken][1];

			if (isDebug()) {
				if ($p1 == $p2) {
					echo "\t", 'Previously only spoken at ', $p1, "\n";
				} else {
					echo "\t", 'Previously spoken at ', $p1, ' and ', $p2, "\n";
				}
			}
			$num = abs($p1 - $p2);

			if (!isset($spoken[$num])) {
				$spoken[$num] = [$i, $i];
			} else {
				$spoken[$num][0] = $spoken[$num][1];
				$spoken[$num][1] = $i;
			}
			$lastSpoken = $num;

			if (isDebug()) { echo sprintf('Turn %6s', $i), ' Spoke number: ', $num, "\n"; }
			$i++;

			if ($i > $turn) { return $lastSpoken; }
		}
	}

	$part1 = getSpokenNumberAt(2020, $input);
	echo 'Part 1: ', $part1, "\n";

	$part2 = getSpokenNumberAt(30000000, $input);
	echo 'Part 2: ', $part2, "\n";
