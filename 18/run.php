#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	function parseExpression($line) {
		$val = 0;
		$line = str_replace('(', ' ( ', str_replace(')', ' ) ', $line));
		$bits = explode(' ', $line);

		$bracketCount = 0;
		$operator = '+';
		$bracketed = [];
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
					$v = parseExpression(implode(' ', $bracketed));
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
				switch ($operator) {
					case '+':
						$val += $v;
						break;
					case '*':
						$val *= $v;
						break;
					default:
						die('Unknown Operator: ' . $operator . "\n");
				}
			}
		}

		return $val;
	}

	$entries = [];
	foreach ($input as $line) {
		$entries[] = [$line, parseExpression($line)];
	}

	$part1 = array_sum(array_column($entries, 1));
	echo 'Part 1: ', $part1, "\n";

	/* $part2 = -1;
	echo 'Part 2: ', $part2, "\n"; */
