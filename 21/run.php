#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	// Map of allergens to the list of ingredients that might contain them
	$possibleAllergens = [];
	// Count of how many times each ingredient appeared somewhere (part 1)
	$ingredients = [];
	foreach ($input as $line) {
		preg_match('#^(.*) \(contains (.*)\)$#SADi', $line, $m);

		$thisIngredients = explode(' ', $m[1]);
		foreach ($thisIngredients as $ingredient) {
			if (!isset($ingredients[$ingredient])) { $ingredients[$ingredient] = 0; }
			$ingredients[$ingredient]++;
		}

		foreach (explode(', ', $m[2]) as $allergen) {
			if (!isset($possibleAllergens[$allergen])) { $possibleAllergens[$allergen] = []; }
			$possibleAllergens[$allergen][] = $thisIngredients;
		}
	}

	$isDebug = isDebug();

	// $allergens is our confirmed list, $possibleAllergens is our unconfirmed.
	// We'll populate $allergens as we go.
	$allergens = [];
	while (count($allergens) != count($possibleAllergens)) {
		// Looking at each possible allergen.
		foreach (array_keys($possibleAllergens) as $a) {
			// If we know of any possible ingredient lists for this allergen
			if (is_array($possibleAllergens[$a])) {
				if ($isDebug) {
					echo 'Checking: ', $a, "\n";
				}

				// We're looking for ingredients that are present in ALL of our
				// ingredient lists.
				//
				// eg, dairy has 2 lists:
				//  - mxmxvkd kfcds sqjhc nhms
				//  - trh fvjkl sbzzf mxmxvkd
				//
				// So we want to loop though each allergen in the first list
				// and look for any that are present in all the others.
				$inAll = [];
				foreach ($possibleAllergens[$a][0] as $i) {
					// Ignore ingredients that we have already matched to an
					// allergen.
					if (in_array($i, $allergens)) { continue; }

					$isInAll = true;
					// Looking at all the other ingredient lists for this
					// allergen.
					for ($k = 1; $k < count($possibleAllergens[$a]); $k++) {
						// Check for the current ingredient we are looking for.
						if (!in_array($i, $possibleAllergens[$a][$k])) {
							$isInAll = false;
							break;
						}
					}

					// Store a list of any ingredients in all lists.
					if ($isInAll) {
						$inAll[] = $i;
					}
				}

				// If there is only 1 in all the lists, then that has to be the
				// right one for this allergen.
				// Otherwise we're not yet sure and should come back to this
				// later.
				if (count($inAll) == 1) {
					// Store the mapping of allergen => ingredient
					$allergens[$a] = $inAll[0];
					// Stop us looping through this possible allergen later.
					$possibleAllergens[$a] = FALSE;
					if ($isDebug) {
						echo "\t", 'Discovered: ', $a, ' is ', $allergens[$a], "\n";
					}
				} else if ($isDebug) {
					echo "\t", 'One of: ', implode(', ', $inAll), "\n";
				}
			}
		}
	}

	$part1 = 0;
	// Count the ingredients for part 1.
	foreach ($ingredients as $i => $c) {
		if (!in_array($i, $allergens)) {
			$part1 += $c;
		}
	}
	echo 'Part 1: ', $part1, "\n";

	// Easy win part 2.
	ksort($allergens);
	$part2 = implode(',', array_values($allergens));

	echo 'Part 2: ', $part2, "\n";
