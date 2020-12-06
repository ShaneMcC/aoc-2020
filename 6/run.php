#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');

	$answers = [];
	$answer = [];
	foreach (explode("\n", getInputContent()) as $line) {
		if (empty($line)) {
			if (count($answer) > 0) { $answers[] = $answer; }
			$answer = [];
		} else {
			foreach (str_split($line) as $bit) {
				$answer[$bit] = true;
			}
		}
	}
	if (count($answer) > 0) { $answers[] = $answer; }

	$part1 = 0;
	foreach ($answers as $group) {
		$part1 += count(array_keys($group));
	}

	echo 'Part 1:', $part1, "\n";
