#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	foreach ($input as $one) {
		foreach ($input as $two) {
			if ($one + $two == 2020) {
				echo $one * $two;
				break 2;
			}
		}
	}
