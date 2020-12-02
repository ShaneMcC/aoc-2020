#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$part1 = 0;
	$entries = [];
	foreach ($input as $line) {
		preg_match('#(.*)-(.*) (.*): (.*)#SADi', $line, $m);
		[$all, $start, $end, $char, $password] = $m;

		$c = substr_count($password, $char);
		$valid = $c >= $start && $c <= $end;

		if ($valid) { $part1++; }

		$entries[] = ['start' => $start, 'end' => $end, 'char' => $char, 'password' => $password, 'valid' => $valid];
	}

	// var_dump($entries);

	echo 'Part 1: ', $part1, "\n";
