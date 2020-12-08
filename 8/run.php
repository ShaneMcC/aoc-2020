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

	function testCode($input) {
		$vm = new Day8VM($input);
		$vm->setDebug(isDebug());

		$visited = [];
		while (true) {
			if (!isset($visited[$vm->getLocation()])) { $visited[$vm->getLocation()] = 0; }
			$visited[$vm->getLocation()]++;
			if (!$vm->step()) { break; }

			if (isset($visited[$vm->getLocation()]) && $visited[$vm->getLocation()] >= 100) {
				return FALSE;
			}
		}

		return $vm->accum;
	}


	$originalInput = $input;
	for ($i = 0; $i < count($input); $i++) {
		$input = $originalInput;
		$line = $input[$i];

		if (startsWith($line, "acc")) { continue; }

		if (startsWith($line, "nop")) {
			$input[$i] = str_replace('jmp', 'jmp', $line);
		} else if (startsWith($line, "jmp")) {
			$input[$i] = str_replace('jmp', 'nop', $line);
		}

		$res = testCode($input);
		if ($res !== FALSE) {
			echo 'Part 2: ', $res, "\n";
			break;
		}
	}
