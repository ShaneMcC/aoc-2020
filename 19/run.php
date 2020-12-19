#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	function buildRegex($ruleStrings, $overrides = []) {
		$definitions = [];

		foreach ($ruleStrings as $ruleString) {
			[$ruleId, $ruleData] = explode(': ', $ruleString, 2);

			$definition = "\n\t" . '# ' . $ruleString . "\n";
			$definition .= "\t" . '(?<r' . $ruleId . '>';

			if (isset($overrides[$ruleId])) {
				$definition .= $overrides[$ruleId];
			} else {
				$options = [];

				foreach (explode(' | ', $ruleData) as $rulebit) {
					if (preg_match('#"(.*)"#', $rulebit, $m)) {
						$options[] = $m[1];
					} else {
						$options[] = '(?&r' . implode(')(?&r', explode(' ', $rulebit)) . ')';
					}
				}

				$definition .= '(' . implode('|', $options) . ')';
			}

			$definition .= ')';


			$definitions[$ruleId] = $definition;
		}
		ksort($definitions);

		$regex = '/';
		$regex .= '(?(DEFINE)';
		foreach ($definitions as $definition) { $regex .= $definition . "\n"; }
		$regex .= ')' . "\n\n";
		$regex .= '# Match rule 0.' . "\n";
		$regex .= '^(?&r0)$' . "\n";
		$regex .= '/x';

		return $regex;
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

	if (isDebug()) {
		echo '========================================', "\n";
		echo 'Part 1 Regex', "\n";
		echo '========================================', "\n";
		echo buildRegex($input[0]);
		echo '========================================', "\n";

		echo '========================================', "\n";
		echo 'Part 2 Regex', "\n";
		echo '========================================', "\n";
		echo buildRegex($input[0], $overrides);
		echo '========================================', "\n";
	}
	echo 'Part 1: ', countMatches($input[1], buildRegex($input[0])), "\n";
	echo 'Part 2: ', countMatches($input[1], buildRegex($input[0], $overrides)), "\n";
