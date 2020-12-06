#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');

	$groups = [];
	$group = ['members' => 0, 'answers' => []];
	foreach (explode("\n", getInputContent()) as $line) {
		if (empty($line)) {
			if (count($group) > 0) { $groups[] = $group; }
			$group = ['members' => 0, 'answers' => []];
		} else {
			$group['members']++;

			foreach (str_split($line) as $bit) {
				if (!isset($group['answers'][$bit])) { $group['answers'][$bit] = 0; }
				$group['answers'][$bit]++;
			}
		}
	}
	if (count($group) > 0) { $groups[] = $group; }

	$part2 = $part1 = 0;
	foreach ($groups as $group) {
		$part1 += count(array_keys($group['answers']));

		foreach ($group['answers'] as $a => $c) {
			if ($c == $group['members']) {
				$part2++;
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
