#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$memory = [];
	$entries = [];
	foreach ($input as $line) {
		if (preg_match('#mask = (.*)#SADi', $line, $m)) {
			if (!empty($section)) { $entries[] = $section; }

			$section = [];
			$section['val'] = str_repeat('0', 36);
			$section['mask'] = $m[1];
			$section['mem'] = [];
		} else if (preg_match('#mem\[(.*)\] = (.*)#SADi', $line, $m)) {
			$section['mem'][] = [$m[1] => $m[2]];
		}
	}
	if (!empty($section)) { $entries[] = $section; }


	foreach ($entries as $section) {
		foreach ($section['mem'] as $mem) {
			foreach ($mem as $loc => $val) {
				$val = decbin($val);
				$val = str_repeat(0, 36 - strlen($val)) . $val;

				for ($i = 0; $i < strlen($section['mask']); $i++) {
					if ($section['mask'][$i] == 'X') { continue; }

					$val[$i] = $section['mask'][$i];
				}

				$memory[$loc] = bindec($val);
			}

		}
	}


	$part1 = array_sum($memory);
	echo 'Part 1: ', $part1, "\n";

//	$part2 = -1;
//	echo 'Part 2: ', $part2, "\n";
