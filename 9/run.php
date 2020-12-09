#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$preambleLen = isTest() ? 5 : 25;

	function findSum($preamble, $sum) {
		for ($i = 0; $i < count($preamble); $i++) {
			for ($j = $i; $j < count($preamble); $j++) {
				if ($preamble[$i] + $preamble[$j] == $sum) {
					return true;
				}
			}
		}

		return false;
	}

	$part1 = 0;
	for ($p = $preambleLen; $p < count($input); $p++) {
		$sum = $input[$p];
		$preamble = array_slice($input, $p - $preambleLen, $preambleLen);

		if (!findSum($preamble, $sum)) {
			$part1 = $sum;
			break;
		}
	}

	echo 'Part 1: ', $part1, "\n";

	for ($i = 0; $i < count($input); $i++) {
		$sum = [$input[$i]];

		for ($j = $i + 1; $j < count($input); $j++) {
			$sum[] = $input[$j];

			$s = array_sum($sum);
			if ($s == $part1) {
				$min = min($sum);
				$max = max($sum);

				$part2 = $min + $max;

				echo 'Part 2: ', $part2, "\n";
			} else if ($s > $part1) {
				break;
			}
		}

	}
