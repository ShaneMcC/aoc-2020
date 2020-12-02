#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$part1 = $part2 = 0;
	foreach ($input as $line) {
		preg_match('#([0-9]+)-([0-9]+) ([a-z]): ([a-z]+)#', $line, $m);
		[$all, $start, $end, $char, $password] = $m;

		$c = substr_count($password, $char);
		if ($c >= $start && $c <= $end) {
			$part1++;
		}

		if ($password[$start - 1] != $password[$end - 1] && ($password[$start - 1] == $char || $password[$end - 1] == $char)) {
			$part2++;
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
