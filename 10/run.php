#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	/* $current = 0;
	$chain = [];
	while (count($chain) < count($input)) {
		$next1 = array_search($current + 1, $input);
		$next2 = array_search($current + 2, $input);
		$next3 = array_search($current + 3, $input);

		if ($next1 !== FALSE) {
			$chain[] = $input[$next1];
			$current = $input[$next1];
		} else if ($next2 !== FALSE) {
			$chain[] = $input[$next2];
			$current = $input[$next2];
		} else if ($next3 !== FALSE) {
			$chain[] = $input[$next3];
			$current = $input[$next3];
		}
	}
	$foo = $chain; */

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

	echo $one, "\n";
	echo $three, "\n";
	$part1 = $one * $three;
	echo 'Part 1: ', $part1, "\n";
