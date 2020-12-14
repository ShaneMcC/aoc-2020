#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		if (preg_match('#mask = (.*)#SADi', $line, $m)) {
			if (!empty($section)) { $entries[] = $section; }

			$section = [];
			$section['val'] = str_repeat('0', 36);
			$section['mask'] = $m[1];
			$section['mem'] = [];
		} else if (preg_match('#mem\[(.*)\] = (.*)#SADi', $line, $m)) {
			$section['mem'][] = [$m[1] => $m[2]];
		}
	}
	if (!empty($section)) { $entries[] = $section; }

	function getPaddedBin($val, $len = 36) {
		$val = decbin($val);
		$val = str_repeat(0, 36 - strlen($val)) . $val;
		return $val;
	}

	function maskValue($val, $mask, $ignore) {
		for ($i = 0; $i < strlen($mask); $i++) {
			if (in_array($mask[$i], $ignore)) { continue; }

			$val[$i] = $mask[$i];
		}

		return $val;
	}

	function getMemoryWithMaskedValues($entries) {
		$memory = [];

		foreach ($entries as $section) {
			foreach ($section['mem'] as $mem) {
				foreach ($mem as $loc => $val) {
					$val = getPaddedBin($val);
					$val = maskValue($val, $section['mask'], ['X']);

					$memory[$loc] = bindec($val);
				}

			}
		}

		return $memory;
	}

	function getPossible($maskedMem) {
		$possibleMems = [$maskedMem];
		for ($i = 0; $i < strlen($maskedMem); $i++) {
			if ($maskedMem[$i] == 'X') {
				$newPossible = [];
				foreach ($possibleMems as $p) {
					$p[$i] = '0';
					$newPossible[] = $p;

					$p[$i] = '1';
					$newPossible[] = $p;
				}

				$possibleMems = $newPossible;

				if (count($possibleMems) > 1024) { die('Aborting due to excessive masking.' . "\n"); }
			}
		}

		return $possibleMems;
	}

	function getMemoryWithMaskedMemory($entries) {
		$memory = [];
		foreach ($entries as $section) {
			foreach ($section['mem'] as $mems) {
				foreach ($mems as $mem => $val) {
					$mem = getPaddedBin($mem);
					$maskedMem = maskValue($mem, $section['mask'], ['0']);

					if (isDebug()) {
						echo '====================', "\n";
						echo sprintf('%25s', 'Memory Location: '), $mem, ' (', bindec($mem), ')', "\n";
						echo sprintf('%25s', 'Mask: '), $section['mask'], "\n";
						echo sprintf('%25s', 'Masked Memory: '), $maskedMem, "\n";
						echo '==========', "\n";
						echo sprintf('%25s', 'Write Value: '), $val, "\n";
					}

					$i = 1;
					foreach (getPossible($maskedMem) as $loc) {
						if (isDebug()) {
							echo sprintf('%25s', 'Write Location #' . $i++ . ': '), $loc, ' (', bindec($loc), ')', "\n";
						}
						$memory[bindec($loc)] = $val;
					}
					if (isDebug()) {
						echo '====================', "\n\n";
					}
				}
			}
		}
		return $memory;
	}

	$part1 = array_sum(getMemoryWithMaskedValues($entries));
	echo 'Part 1: ', $part1, "\n";

	$part2 = array_sum(getMemoryWithMaskedMemory($entries));
	echo 'Part 2: ', $part2, "\n";
