#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	require_once(dirname(__FILE__) . '/../common/Day8VM.php');
	$input = getInputLines();
	$originalInput = $input;

	function codeHasExit($input, $loopValue = 10) {
		$vm = new Day8VM($input);
		$vm->setDebug(isDebug());

		$visited = [];
		while (true) {
			if (!isset($visited[$vm->getLocation()])) { $visited[$vm->getLocation()] = 0; }
			$visited[$vm->getLocation()]++;
			if (!$vm->step()) { break; }

			if (isset($visited[$vm->getLocation()]) && $visited[$vm->getLocation()] >= $loopValue) {
				return [FALSE, $vm->getAccumulator()];
			}
		}

		return [TRUE, $vm->getAccumulator()];
	}

	$part1 = codeHasExit($input, 1);
	echo 'Part 1: ', $part1[1], "\n";

	for ($i = 0; $i < count($input); $i++) {
		$input = $originalInput;
		$line = $input[$i];

		if (startsWith($line, "acc")) { continue; }

		if (startsWith($line, "nop")) {
			$input[$i] = str_replace('jmp', 'jmp', $line);
		} else if (startsWith($line, "jmp")) {
			$input[$i] = str_replace('jmp', 'nop', $line);
		}

		$part2 = codeHasExit($input);
		if ($part2[0] !== FALSE) {
			echo 'Part 2: ', $part2[1], "\n";
			break;
		}
	}
