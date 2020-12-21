#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$possibleAllergens = [];
	$allergens = [];
	$ingredients = [];
	foreach ($input as $line) {
		preg_match('#^(.*) \(contains (.*)\)$#SADi', $line, $m);

		$ings = explode(' ', $m[1]);
		foreach ($ings as $ing) {
			if (!isset($ingredients[$ing])) { $ingredients[$ing] = 0; }
			$ingredients[$ing]++;
		}

		foreach (explode(', ', $m[2]) as $a) {
			if (!isset($possibleAllergens[$a])) { $possibleAllergens[$a] = []; }
			$possibleAllergens[$a][] = $ings;
		}
	}

	while (count($allergens) != count($possibleAllergens)) {
		foreach (array_keys($possibleAllergens) as $a) {
			if (is_array($possibleAllergens[$a])) {

				$inAll = [];
				foreach ($possibleAllergens[$a][0] as $i) {
					if (in_array($i, $allergens)) { continue; }

					$isInAll = true;
					for ($k = 1; $k < count($possibleAllergens[$a]); $k++) {
						if (!in_array($i, $possibleAllergens[$a][$k])) {
							$isInAll = false;
						}
					}

					if ($isInAll) {
						$inAll[] = $i;
					}
				}

				if (count($inAll) == 1) {
					$allergens[$a] = $inAll[0];
					$possibleAllergens[$a] = FALSE;
				}
			}
		}
	}

	$part1 = 0;
	foreach ($ingredients as $i => $c) {
		if (!in_array($i, $allergens)) {
			$part1 += $c;
		}
	}
	echo 'Part 1: ', $part1, "\n";

	ksort($allergens);
	$part2 = implode(',', array_values($allergens));

	echo 'Part 2: ', $part2, "\n";
