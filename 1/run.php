#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	function part1($input) {
		for ($i = 0; $i < count($input); $i++) {
			for ($j = $i + 1; $j < count($input); $j++) {
				if ($input[$i] + $input[$j] == 2020) {
					return [$input[$i], $input[$j]];
				}
			}
		}
	}

	function part2($input) {
		for ($i = 0; $i < count($input); $i++) {
			for ($j = $i + 1; $j < count($input); $j++) {
				for ($k = $j + 1; $k < count($input); $k++) {
					if ($input[$i] + $input[$j] + $input[$k] == 2020) {
						return [$input[$i], $input[$j], $input[$k]];
					}
				}
			}
		}
	}

	$part1 = part1($input);
	echo 'Part 1: ', $part1[0] ,' * ', $part1[1], ' = ', $part1[0] * $part1[1], "\n";

	$part2 = part2($input);
	echo 'Part 2: ', $part2[0], ' * ', $part2[1], ' * ', $part2[2], ' = ', $part2[0] * $part2[1] * $part2[2], "\n";
