#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$ourPublicKey = $input[0];
	$doorPublicKey = $input[1];

	function getLoopSizeFor($key) {
		$value = 1;
		for ($i = 1 ;; $i++) {
			$value = ($value * 7) % 20201227;

			if ($value == $key) {
				return $i;
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

	$ourLoop = getLoopSizeFor($ourPublicKey);
	$doorLoop = getLoopSizeFor($doorPublicKey);

	if (isDebug()) {
		echo 'Our Loop: ', $ourLoop, "\n";
		echo 'Door Loop: ', $doorLoop, "\n";
	}

	$doorCode = getKey($doorLoop, $ourPublicKey);

	echo 'Part 1 - Door Code: ', $doorCode, "\n";
