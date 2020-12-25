#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	function getLoopSizeFor($keys) {
		$value = 1;
		for ($i = 1 ;; $i++) {
			$value = ($value * 7) % 20201227;

			if ($c = array_search($value, $keys)) {
				return [$c, $i];
			}
		}
	}

	function getKey($loopSize, $subject) {
		$value = 1;
		for ($i = 0; $i < $loopSize; $i++) {
			$value = ($value * $subject) % 20201227;
		}

		return $value;
	}

	[$whichKey, $loopSize] = getLoopSizeFor($input);
	$whichKey = ($whichKey == 0) ? 1 : 0;
	$doorCode = getKey($loopSize, $input[$whichKey]);

	echo 'Part 1 - Door Code: ', $doorCode, "\n";
