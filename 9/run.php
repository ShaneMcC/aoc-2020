#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$preambleLen = isTest() ? 5 : 25;

	function findSum($input, $p, $preambleLen) {
		for ($i = $p - $preambleLen; $i < $p; $i++) {
			for ($j = $i; $j < $p; $j++) {
				if ($input[$i] + $input[$j] == $input[$p]) {
					return true;
				}
			}
		}

		return false;
	}

	function getInvalidNumber($input, $preambleLen) {
		for ($p = $preambleLen; $p < count($input); $p++) {
			if (!findSum($input, $p, $preambleLen)) {
				return $input[$p];
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
