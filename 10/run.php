#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();
	sort($input);

	$startingPosition = 0;
	$maxRange = 3;
	$maxValue = max($input) + $maxRange;

	function getPart1($input, $maxRange) {
		$prev = $one = $three = 0;
		foreach ($input as $i) {
			if ($prev + 1 == $i) { $one++; }
			else if ($prev + 3 == $i) { $three++; }
			$prev = $i;
		}
		if ($maxRange == 3) { $three++; }

		return $one * $three;
	}

	$part1 = getPart1($input, $maxRange);
	echo 'Part 1: ', $part1, "\n";

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
	foreach (getOptions($input, $startingPosition, $maxRange) as $o) { $options[$o] = ['value' => $o, 'count' => 1]; }

	while (!empty($options)) {
		$firstKey = array_keys($options)[0];
		$val = $options[$firstKey]['value'];
		$count = $options[$firstKey]['count'];
		unset($options[$firstKey]);

		$next = getOptions($input, $val, $maxRange);
		if (empty($next)) {
			if ($val + $maxRange >= $maxValue) {
				$part2 += $count;
			}
		} else {
			foreach ($next as $opt) {
				if (!isset($options[$opt])) { $options[$opt] = ['value' => $opt, 'count' => 0]; }
				$options[$opt]['count'] += $count;
			}
		}
	}

	echo 'Part 2: ', $part2, "\n";
