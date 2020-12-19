#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	function buildDefinitions($ruleStrings, $overrides = []) {
		$definitions = '(?(DEFINE)';

		foreach ($ruleStrings as $ruleString) {
			[$ruleId, $ruleString] = explode(': ', $ruleString, 2);

			$definitions .= '(?<r' . $ruleId . '>';

			if (isset($overrides[$ruleId])) {
				$definitions .= $overrides[$ruleId];
			} else {
				$options = [];

				foreach (explode(' | ', $ruleString) as $rulebit) {
					if (preg_match('#"(.*)"#', $rulebit, $m)) {
						$options[] = $m[1];
					} else {
						$options[] = '(?&r' . implode(')(?&r', explode(' ', $rulebit)) . ')';
					}
				}

				$definitions .= '(' . implode('|', $options) . ')';
			}

			$definitions .= ')'."\n";
		}

		$definitions .= ')';

		return $definitions;
	}

	$overrides = [];

	// 8: 42 | 42 8
	$overrides[8] = '(?&r42)+';

	// 11: 42 31 | 42 11 31
	$maxLen = 0;
	foreach ($input[1] as $message) { $maxLen = max($maxLen, strlen($message)); }

	$overrides[11] = [];
	for ($i = 1; $i < ($maxLen / 2); $i++) {
		$overrides[11][] = '(?&r42){' . $i . '}(?&r31){' . $i . '}';
	}
	$overrides[11] = '(' . implode('|', $overrides[11]) . ')';

	function countMatches($input, $regex) {
		$count = 0;
		foreach ($input as $message) {
			if (preg_match($regex, $message)) {
				$count++;
			}
		}

		return $count;
	}

	echo 'Part 1: ', countMatches($input[1], '#' . buildDefinitions($input[0]) . '^(?&r0)$#'), "\n";
	echo 'Part 2: ', countMatches($input[1], '#' . buildDefinitions($input[0], $overrides) . '^(?&r0)$#'), "\n";
