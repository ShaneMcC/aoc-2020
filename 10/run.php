#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();
	sort($input);

	$startingPosition = 0;
	$maxRange = 3;
	$maxValue = max($input) + $maxRange;

	function getPart1($input, $maxRange) {
		$prev = 0;
		$possible = range(1, $maxRange);
		$counts = array_fill_keys($possible, 0);
		foreach ($input as $i) {
			foreach ($possible as $p) {
				if ($prev + $p == $i) { $counts[$p]++; }
			}
			$prev = $i;
		}
		$counts[$maxRange]++; // Max Difference

		return $counts;
	}

	$part1 = getPart1($input, $maxRange);
	echo 'Part 1 ', json_encode($part1), ': ', $part1[1] * ($part1[3]), "\n";

	function getOptions($input, $current, $maxRange = 3) {
		$options = [];

		for ($i = 1; $i <= $maxRange; $i++) {
			$next = array_search($current + $i, $input);
			if ($next !== FALSE) { $options[] = $input[$next]; }
		}

		return $options;
	}

	$part2 = 0;
	$options = [];

	// Initial options from starting position
	foreach (getOptions($input, $startingPosition, $maxRange) as $o) { $options[$o] = 1; }

	while (!empty($options)) {
		$val = array_keys($options)[0];
		$count = $options[$val];
		unset($options[$val]);

		$next = getOptions($input, $val, $maxRange);
		if (empty($next)) {
			if ($val + $maxRange >= $maxValue) {
				$part2 += $count;
			}
		} else {
			foreach ($next as $opt) {
				if (!isset($options[$opt])) { $options[$opt] = 0; }
				$options[$opt] += $count;
			}
		}
	}

	echo 'Part 2: ', $part2, "\n";
