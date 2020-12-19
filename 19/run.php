#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$rules = [];

	function createRule($ruleString) {
		$bits = explode(': ', $ruleString, 2);

		$rule = ['str' => $bits[1], 'rules' => []];
		foreach (explode(' | ', $bits[1]) as $rulebit) {
			$rule['rules'][] = trim($rulebit);
		}

		return [$bits[0], $rule];
	}

	foreach ($input[0] as $rule) {
		[$rid, $r] = createRule($rule);
		$rules[$rid] = $r;
	}

	function buildRegex($rules, $ruleId, $overrides = []) {
		if (isset($overrides[$ruleId])) {
			return $overrides[$ruleId];
		}

		$ruleinfo = $rules[$ruleId]['rules'];

		$options = [];
		foreach ($ruleinfo as $rule) {
			if (preg_match('#"(.*)"#', $rule, $m)) {
				$options[] = $m[1];
			} else {
				$ruleOptions = [];
				foreach (explode(' ', $rule) as $rid) {
					$ruleOptions[] = buildRegex($rules, $rid, $overrides);
				}
				$options[] = (count($ruleOptions) == 1) ? $ruleOptions[0] : implode($ruleOptions);
			}
		}

		return (count($options) == 1) ? $options[0] : '(' . implode('|', $options) . ')';
	}

	$part1regex = '#^' . buildRegex($rules, 0) . '$#';

	$part1 = 0;
	foreach ($input[1] as $message) {
		if (preg_match($part1regex, $message)) {
			$part1++;
		}
	}
	echo 'Part 1: ', $part1, "\n";

	$overrides = [];
	$maxLen = 0;
	foreach ($input[1] as $message) { $maxLen = max($maxLen, strlen($message)); }

	// $rules[8] = createRule('8: 42 | 42 8')[1];
	$overrides[8] = '(' . buildRegex($rules, 42) . ')+';

	// $rules[11] = createRule('11: 42 31 | 42 11 31')[1];
	$overrides[11] = [];
	$def = '(?(DEFINE)(?<r42>' . buildRegex($rules, 42) . ')(?<r31>' . buildRegex($rules, 31) . '))';
	for ($i = 1; $i < ($maxLen / 2); $i++) {
		$repeat = '{' . $i . '}';
		$overrides[11][] = '(?&r42)' . $repeat . '(?&r31)' . $repeat;
	}
	$overrides[11] = $def . '(' . implode('|', $overrides[11]) . ')';

	$part2regex = '#^' . buildRegex($rules, 0, $overrides) . '$#';

	$part2 = 0;
	foreach ($input[1] as $message) {
		if (preg_match($part2regex, $message)) {
			$part2++;
		}
	}
	echo 'Part 2: ', $part2, "\n";
