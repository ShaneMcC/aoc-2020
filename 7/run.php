#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$bags = [];
	foreach ($input as $line) {
		preg_match('#(.*) bags? contains? (.*)#SADi', $line, $m);
		[$all, $source, $contains] = $m;

		$bags[$source] = [];
		foreach (explode(', ', $contains) as $c) {
			if (preg_match('#([0-9]+) (.*) bags?#SADi', $c, $m)) {
				$bags[$source][$m[2]] = $m[1];
			}
		}
	}

	function hasBag($bags, $starter, $wanted) {
		$queue = [$starter];
		while (!empty($queue)) {
			$type = array_pop($queue);
			foreach ($bags[$type] as $t => $c) {
				$queue[] = $t;

				if ($t == $wanted) { return true; }
			}
		}

		return false;
	}

	$part1 = 0;
	foreach (array_keys($bags) as $t) {
		if (hasBag($bags, $t, 'shiny gold')) {
			$part1++;
		}
	}

	echo 'Part 1: ', $part1, "\n";
