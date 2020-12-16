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

	$myTicket = $input[1][1];

	$nearbyTickets = $input[2];
	array_shift($nearbyTickets);

	function getValidFields($fields, $value) {
		$valid = [];

		foreach ($fields as $name => $v) {
			foreach ($v as $val) {
				if ($value >= $val['min'] && $value <= $val['max']) {
					$valid[] = $name;
					break;
				}
			}
		}

		return $valid;
	}

	$invalidValues = [];
	foreach ($nearbyTickets as $t) {
		$values = explode(',', $t);

		foreach ($values as $value) {
			$valid = getValidFields($fields, $value);

			if (empty($valid)) {
				$invalidValues[] = $value;
			}
		}
	}

	$part1 = array_sum($invalidValues);
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
