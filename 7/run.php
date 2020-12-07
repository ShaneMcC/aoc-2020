#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$bags = [];
	foreach ($input as $line) {
		[$all, $source, $contains] = preg_match_return('#(.*) bags? contains? (.*)#SADi', $line);

		if (!isset($bags[$source])) { $bags[$source] = ['container' => [], 'bags' => []]; }
		foreach (explode(', ', $contains) as $c) {
			if ([$all, $count, $type] = preg_match_return('#([0-9]+) (.*) bags?#SADi', $c)) {
				$bags[$source]['bags'][$type] = $count;

				if (!isset($bags[$type])) { $bags[$type] = ['container' => [], 'bags' => []]; }
				$bags[$type]['container'][] = $source;
			}
		}
	}

	function countContainers($bags, $wanted) {
		$count = 0;
		$found = [];
		$queue = [$wanted];
		while (!empty($queue)) {
			$type = array_pop($queue);

			foreach ($bags[$type]['container'] as $t) {
				if (!isset($found[$t])) {
					$found[$t] = true;
					$count++;
					$queue[] = $t;
				}
			}
		}

		return $count;
	}

	function countBags($bags, $type) {
		$count = 1;

		foreach ($bags[$type]['bags'] as $t => $c) {
			$count += countBags($bags, $t) * $c;
		}

		return $count;
	}

	$part1 = countContainers($bags, 'shiny gold');
	$part2 = countBags($bags, 'shiny gold') - 1;

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
