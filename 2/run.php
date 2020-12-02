#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$part1 = 0;
	$part2 = 0;
	$entries = [];
	foreach ($input as $line) {
		preg_match('#(.*)-(.*) (.*): (.*)#SADi', $line, $m);
		[$all, $start, $end, $char, $password] = $m;

		$c = substr_count($password, $char);
		$valid1 = $c >= $start && $c <= $end;

		if ($valid1) { $part1++; }

		$valid2 = $password[$start - 1] != $password[$end - 1] && ($password[$start - 1] == $char || $password[$end - 1] == $char);
		if ($valid2) { $part2++; }

		$entries[] = ['start' => $start, 'end' => $end, 'char' => $char, 'password' => $password, 'valid1' => $valid2, 'valid2' => $valid2];
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
