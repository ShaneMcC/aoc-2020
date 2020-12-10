#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$foo = $input;
	sort($foo);
	$prev = $one = $three = 0;
	foreach ($foo as $i) {
		if ($prev + 1 == $i) {
			$one++;
		} else if ($prev + 3 == $i) {
			$three++;
		}
		$prev = $i;
	}
	$three++;

	$part1 = $one * $three;
	echo 'Part 1: ', $part1, "\n";

	function getOptions($input, $current) {
		$options = [];

		$next1 = array_search($current + 1, $input);
		$next2 = array_search($current + 2, $input);
		$next3 = array_search($current + 3, $input);

		if ($next1 !== FALSE) { $options[] = $input[$next1]; }
		if ($next2 !== FALSE) { $options[] = $input[$next2]; }
		if ($next3 !== FALSE) { $options[] = $input[$next3]; }

		return $options;
	}

	$max = max($input) + 3;

	$valid = 0;
	$options = [];
	foreach (getOptions($input, 0) as $o) { $options[$o] = ['count' => 1]; }

	while (!empty($options)) {
		$k = array_keys($options);
		$last = array_shift($k);
		$c = $options[$last]['count'];
		unset($options[$last]);

		$v = getOptions($input, $last);
		if (empty($v)) {
			if ($last + 3 >= $max) {
				$valid += $c;
			}
		} else {
			foreach ($v as $o) {
				if (!isset($options[$o])) { $options[$o] = ['count' => 0]; }
				$options[$o]['count'] += $c;
			}
		}
	}

	$part2 = $valid;
	echo 'Part 2: ', $part2, "\n";
