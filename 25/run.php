#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$ourPublicKey = $input[0];
	$doorPublicKey = $input[1];

	function getKey($loopSize, $subject) {
		$value = 1;
		for ($i = 0; $i < $loopSize; $i++) {
			$value *= $subject;
			$value = $value % 20201227;
		}

		return $value;
	}

	function getKeyFor($key) {
		$i = 0;
		$subject = 7;
		$value = 1;
		while (true) {
			$i++;
			$value *= $subject;
			$value = $value % 20201227;

			if ($value == $key) {
				return $i;
			}
		}
	}

	$ourLoop = getKeyFor($ourPublicKey);
	$doorLoop = getKeyFor($doorPublicKey);

	if (isDebug()) {
		echo 'Our Loop: ', $ourLoop, "\n";
		echo 'Door Loop: ', $doorLoop, "\n";
	}

	$doorCode = getKey($doorLoop, $ourPublicKey);

	echo 'Part 1 - Door Code: ', $doorCode, "\n";
