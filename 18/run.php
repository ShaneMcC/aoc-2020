#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	function parseExpression($line, $precedence = ['+*']) {
		$line = str_replace('(', ' ( ', str_replace(')', ' ) ', $line));

		$bracketCount = 0;
		$bracketed = [];

		$expression = ['0'];
		$operator = '+';

		foreach (explode(' ', $line) as $v) {
			if (empty($v)) { continue; }

			if ($v == ')') {
				$bracketCount--;
				$bracketed[] = $v;
				if ($bracketCount == 0) {
					$bracketedExpression = implode(' ', array_splice($bracketed, 1, count($bracketed) - 1));
					$bracketed = [];

					$expression[] = $operator;
					$expression[] = parseExpression($bracketedExpression, $precedence);
				}
			} else if ($v == '(') {
				$bracketCount++;
				$bracketed[] = $v;
			} else if ($bracketCount > 0) {
				$bracketed[] = $v;
			} else if (!is_numeric($v)) {
				$operator = $v;
			} else {
				$expression[] = $operator;
				$expression[] = $v;
			}
		}

		$expression = implode(' ', $expression);
		foreach ($precedence as $p) {
			do {
				$changed = false;
				$expression = preg_replace_callback('/([0-9]+) ([' . preg_quote($p) . ']) ([0-9]+)/', function ($m) use (&$changed) {
					switch ($m[2]) {
						case '+':
							$changed = true;
							return $m[1] + $m[3];
						case '*':
							$changed = true;
							return $m[1] * $m[3];
					}

					die('Unable to handle expression.');
				}, $expression, 1);
			} while ($changed);
		}

		return $expression;
	}

	$entries = [];
	foreach ($input as $line) {
		$entries[] = [$line, parseExpression($line, ['+*']), parseExpression($line, ['+', '*'])];
	}

	$part1 = array_sum(array_column($entries, 1));
	echo 'Part 1: ', $part1, "\n";

	$part2 = array_sum(array_column($entries, 2));
	echo 'Part 2: ', $part2, "\n";
