#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	require_once(dirname(__FILE__) . '/../common/Day8VM.php');
	$input = getInputLines();

	$vm = new Day8VM($input);

	$vm->setDebug(isDebug());

	$visited = [];
	while (true) {
		$visited[$vm->getLocation()] = true;
		$vm->step();

		if (isset($visited[$vm->getLocation()]) && $visited[$vm->getLocation()] == true) {
			echo 'Part 1: ', $vm->accum, "\n";
			break;
		}
	}
	$vm->reset();

