#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$rules = [];
	foreach ($input[0] as $rule) {
		$bits = explode(': ', $rule, 2);

		$r = ['str' => $bits[1], 'rules' => []];
		foreach (explode(' | ', $bits[1]) as $rulebit) {
			$r['rules'][] = trim($rulebit);
		}

		$rules[$bits[0]] = $r;
	}

	function buildRegex($rules, $ruleId) {
		$ruleinfo = $rules[$ruleId]['rules'];

		$options = [];
		foreach ($ruleinfo as $rule) {
			if (preg_match('#"(.*)"#', $rule, $m)) {
				$options[] = $m[1];
			} else {
				$ruleOptions = [];
				foreach (explode(' ', $rule) as $rid) {
					$ruleOptions[] = buildRegex($rules, $rid);
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

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
