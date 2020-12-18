#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	function parseExpression($line, $precedence = ['+*']) {
		$val = 0;
		$line = str_replace('(', ' ( ', str_replace(')', ' ) ', $line));
		$bits = explode(' ', $line);

		$bracketCount = 0;
		$operator = '+';
		$bracketed = [];

		$expression = [0];

		foreach ($bits as $v) {
			if (empty($v)) { continue; }
			if ($v == '(') {
				$bracketCount++;
				if ($bracketCount == 1) {
					$bracketed = [];
				} else {
					$bracketed[] = $v;
				}
			} else if ($v == ')') {
				$bracketCount--;
				if ($bracketCount == 0) {
					$v = parseExpression(implode(' ', $bracketed), $precedence);
				} else {
					$bracketed[] = $v;
				}
			} else if ($bracketCount > 0) {
				$bracketed[] = $v;
				continue;
			} else if (!is_numeric($v)) {
				$operator = $v;
			}

			if (is_numeric($v)) {
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
