#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$players = [];
	foreach ($input as $group) {
		$name = array_shift($group);
		preg_match('#Player (.*):#SADi', $name, $m);
		$name = $m[1];
		$players[$name] = $group;
	}

	function step($game) {
		$card1 = array_shift($game[1]);
		$card2 = array_shift($game[2]);

		if ($card1 > $card2) {
			$game[1][] = $card1;
			$game[1][] = $card2;
		} else {
			$game[2][] = $card2;
			$game[2][] = $card1;
		}

		return $game;
	}


	$game = $players;
	while (!empty($game[1]) && !empty($game[2])) {
		$game = step($game);
	}

	$winner = empty($game[1]) ? 2 : 1;

	$part1 = 0;
	for ($i = 1; $i <= count($game[$winner]); $i++) {
		$v = (count($game[$winner]) - $i) + 1;
		echo $v, ' * ', $game[$winner][$i - 1], "\n";
		$part1 += ($v * $game[$winner][$i - 1]);
	}

	echo 'Part 1: ', $part1, "\n";
