#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	function part1($input) {
		foreach ($input as $one) {
			foreach ($input as $two) {
				if ($one + $two == 2020) {
					return [$one, $two];
				}
			}
		}
	}

	function part2($input) {
		foreach ($input as $one) {
			foreach ($input as $two) {
				foreach ($input as $three) {
					if ($one + $two + $three == 2020) {
						return [$one, $two, $three];
					}
				}
			}
		}
	}

	$part1 = part1($input);
	echo 'Part 1: ', $part1[0] ,' * ', $part1[1], ' = ', $part1[0] * $part1[1], "\n";

	$part2 = part2($input);
	echo 'Part 2: ', $part2[0], ' * ', $part2[1], ' * ', $part2[2], ' = ', $part2[0] * $part2[1] * $part2[2], "\n";
