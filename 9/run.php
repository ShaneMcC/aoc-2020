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

	function getInvalidNumber($input, $preambleLen) {
		for ($p = $preambleLen; $p < count($input); $p++) {
			$sum = $input[$p];
			$preamble = array_slice($input, $p - $preambleLen, $preambleLen);

			if (!findSum($preamble, $sum)) {
				return $sum;
			}
		}
	}

	function getEncryptionWeakness($input, $invalidNumber) {
		for ($i = 0; $i < count($input); $i++) {
			$min = $max = $sum = $input[$i];

			for ($j = $i + 1; $j < count($input); $j++) {
				$sum += $input[$j];
				$min = min($min, $input[$j]);
				$max = max($max, $input[$j]);

				if ($sum == $invalidNumber) {
					return $min + $max;
				} else if ($sum > $invalidNumber) {
					break;
				}
			}
		}
	}

	$part1 = getInvalidNumber($input, $preambleLen);
	echo 'Part 1: ', $part1, "\n";

	$part2 = getEncryptionWeakness($input, $part1);
	echo 'Part 2: ', $part2, "\n";
