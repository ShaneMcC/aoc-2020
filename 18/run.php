#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	require_once(dirname(__FILE__) . '/test.php');

	$input = getInputLines();

	function parseExpression($expression, $precedence = ['+*']) {
		do {
			$changed = false;
			$expression = preg_replace_callback('/\(([^()]+)\)/', function ($m) use (&$changed, $precedence) {
				$changed = true;
				return parseExpression($m[1], $precedence);
			}, $expression);
		} while ($changed);

		$expression = '0 + ' . $expression;

		foreach ($precedence as $p) {
			$pq = preg_quote($p);
			do {
				$changed = false;
				$expression = preg_replace_callback('/([0-9]+)\s+([' . $pq . '])\s+([0-9]+)/', function ($m) use (&$changed) {
					$changed = true;
					switch ($m[2]) {
						case '+':
							return $m[1] + $m[3];
						case '*':
							return $m[1] * $m[3];
					}
				}, $expression, 1);
			} while ($changed);
		}

		return $expression;
	}

	$part1 = 0;
	$part2 = 0;
	foreach ($input as $line) {
		$part1 += parseExpression($line, ['+*']);
		$part2 += parseExpression($line, ['+', '*']);
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
