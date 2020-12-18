#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');

	if (isTest()) {
		$test = [];
		$test[] = ['1 + 2 * 3 + 4 * 5 + 6', 71, 231];
		$test[] = ['1 + (2 * 3) + (4 * (5 + 6))', 51, 51];
		$test[] = ['2 * 3 + (4 * 5)', 26, 46];
		$test[] = ['5 + (8 * 3 + 9 + 3 * 4 * 3)', 437, 1445];
		$test[] = ['5 * 9 * (7 * 3 * 3 + 9 * 3 + (8 + 6 * 4))', 12240, 669060];
		$test[] = ['((2 + 4 * 9) * (6 + 9 * 8 + 6) + 6) + 2 + 4 * 2', 13632, 23340];

		foreach ($test as $t) {
			echo 'Expression: ', $t[0], "\n";

			$p1 = parseExpression($t[0], ['+*']);
			echo "\t", 'Part 1: ', $p1, ' wanted ', $t[1], ' - ', ($t[1] == $p1 ? 'YES' : 'NO'), "\n";

			$p2 = parseExpression($t[0], ['+', '*']);
			echo "\t", 'Part 2: ', $p2, ' wanted ', $t[2], ' - ', ($t[2] == $p2 ? 'YES' : 'NO'), "\n";
		}
		die();
	}

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
