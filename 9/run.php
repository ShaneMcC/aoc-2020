#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$preambleLen = isTest() ? 5 : 25;

	function findSum($preamble, $sum) {
		for ($i = 0; $i < count($preamble); $i++) {
			for ($j = 0; $j < count($preamble); $j++) {
				if ($preamble[$i] + $preamble[$j] == $sum) {
					echo $preamble[$i], ' + ', $preamble[$j], ' == ', $sum, "\n";

					return true;
				}
			}
		}

		echo 'No ', $sum, "\n";

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
