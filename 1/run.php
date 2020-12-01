#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();
	sort($input);

	function part1($input) {
		for ($i = 0; $i < count($input); $i++) {
			for ($j = $i + 1; $j < count($input); $j++) {
				$sum = $input[$i] + $input[$j];
				if ($sum == 2020) {
					return [$input[$i], $input[$j]];
				} else if ($sum > 2020) {
					break;
				}
			}
		}

		return [0, 0];
	}

	function part2($input) {
		for ($i = 0; $i < count($input); $i++) {
			for ($j = $i + 1; $j < count($input); $j++) {
				for ($k = $j + 1; $k < count($input); $k++) {
					$sum = $input[$i] + $input[$j] + $input[$k];
					if ($sum == 2020) {
						return [$input[$i], $input[$j], $input[$k]];
					} else if ($sum > 2020) {
						break;
					}
				}
			}
		}

		return [0, 0, 0];
	}

	$part1 = part1($input);
	echo 'Part 1: ', $part1[0] ,' * ', $part1[1], ' = ', $part1[0] * $part1[1], "\n";

	$part2 = part2($input);
	echo 'Part 2: ', $part2[0], ' * ', $part2[1], ' * ', $part2[2], ' = ', $part2[0] * $part2[1] * $part2[2], "\n";
