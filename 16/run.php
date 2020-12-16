#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$fields = [];
	foreach ($input[0] as $f) {
		$bits = explode(':', $f, 2);

		$name = $bits[0];
		$fields[$name] = [];

		preg_match_all('#([0-9]+)-([0-9]+)#', $bits[1], $m);
		for ($i = 0; $i < count($m[0]); $i++) {
			$fields[$name][] = ['min' => $m[1][$i], 'max' => $m[2][$i]];
		}
	}

	$myTicket = explode(',', $input[1][1]);

	$nearbyTickets = $input[2];
	array_shift($nearbyTickets);

	function getValidFields($fields, $values, $ignore = []) {
		$valid = [];
		if (!is_array($values)) { $values = [$values]; }

		$debug = count($ignore) > 0;
		$debug = false;

		foreach ($fields as $name => $v) {
			if (in_array($name, $ignore)) { continue; }

			$allValid = true;
			foreach ($values as $testValue) {
				$hasValid = false;
				foreach ($v as $val) {
					if ($testValue >= $val['min'] && $testValue <= $val['max']) {
						$hasValid = true;
						break;
					}
				}

				if (!$hasValid) {
					$allValid = false;
					break;
				}
			}

			if ($allValid) {
				$valid[] = $name;
			}
		}

		return $valid;
	}

	$validTickets = [$myTicket];
	$invalidValues = [];
	foreach ($nearbyTickets as $t) {
		$values = explode(',', $t);

		$isValidTicket = true;

		foreach ($values as $value) {
			$valid = getValidFields($fields, $value);

			if (empty($valid)) {
				$invalidValues[] = $value;
				$isValidTicket = false;
			}
		}

		if ($isValidTicket) {
			$validTickets[] = $values;
		}
	}

	$part1 = array_sum($invalidValues);
	echo 'Part 1: ', $part1, "\n";

	$finalFields = array_fill(0, count($myTicket), FALSE);

	$lastFields = [];

	while (true) {
		for ($i = 0; $i < count($finalFields); $i++) {
			if ($finalFields[$i] !== FALSE) { continue; }
			$values = array_column($validTickets, $i);

			$valid = getValidFields($fields, $values, array_values($finalFields));

			if (count($valid) == 1) {
				$finalFields[$i] = $valid[0];
			}
		}

		// Did we make any changes?
		$thisFields = array_values($finalFields);
		if ($lastFields == $thisFields) { break; }
		$lastFields = $thisFields;
	}

	$part2 = 1;
	foreach ($finalFields as $i => $f) {
		if (startsWith($f, 'departure')) {
			$part2 *= $myTicket[$i];
		}
	}

	echo 'Part 2: ', $part2, "\n";
