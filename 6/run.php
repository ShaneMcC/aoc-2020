#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');

	$answers = [];
	$answer = ['_' => 0];
	foreach (explode("\n", getInputContent()) as $line) {
		if (empty($line)) {
			if (count($answer) > 0) { $answers[] = $answer; }
			$answer = ['_' => 0];
		} else {
			$answer['_']++;

			foreach (str_split($line) as $bit) {
				if (!isset($answer[$bit])) { $answer[$bit] = 0; }
				$answer[$bit]++;
			}
		}
	}
	if (count($answer) > 0) { $answers[] = $answer; }

	$part2 = $part1 = 0;
	foreach ($answers as $group) {
		$part1 += count(array_keys($group)) - 1;

		foreach ($group as $g => $c) {
			if ($g == '_') { continue; }
			if ($c == $group['_']) {
				$part2++;
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
