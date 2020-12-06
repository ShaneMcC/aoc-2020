#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');

	$groups = [];
	foreach (getInputLineGroups() as $lineGroup) {
		$group = ['members' => 0, 'answers' => []];

		foreach ($lineGroup as $line) {
			$group['members']++;

			foreach (str_split($line) as $bit) {
				if (!isset($group['answers'][$bit])) { $group['answers'][$bit] = 0; }
				$group['answers'][$bit]++;
			}
		}

		$groups[] = $group;
	}

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
