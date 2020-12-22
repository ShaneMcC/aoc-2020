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

	function calculateScore($deck) {
		$score = 0;
		for ($i = 1; $i <= count($deck); $i++) {
			$v = (count($deck) - $i) + 1;
			$score += ($v * $deck[$i - 1]);
		}

		return $score;
	}

	function combat($game, $prefix = "\t") {
		$round = 0;
		$isDebug = isDebug();

		if ($isDebug) { echo $prefix, '=== Game ===', "\n"; }

		while (!empty($game[1]) && !empty($game[2])) {
			$round++;
			if ($isDebug) {
				echo "\n", $prefix, '-- Round ' . $round .' --', "\n";
				echo $prefix, 'Player 1\'s deck: ', implode(', ', $game[1]), "\n";
				echo $prefix, 'Player 2\'s deck: ', implode(', ', $game[2]), "\n";
			}

			$card1 = array_shift($game[1]);
			$card2 = array_shift($game[2]);
			$winner = ($card1 > $card2) ? 1 : 2;

			if ($isDebug) {
				echo $prefix, 'Player 1 plays: ', $card1, "\n";
				echo $prefix, 'Player 2 plays: ', $card2, "\n";
				echo $prefix, 'Player ' . $winner . ' wins the round!', "\n";
			}

			if ($winner == 1) {
				$game[1] = array_merge($game[1], [$card1, $card2]);
			} else {
				$game[2] = array_merge($game[2], [$card2, $card1]);
			}
		}

		$winner = empty($game[1]) ? 2 : 1;

		if ($isDebug) {
			echo $prefix, 'The winner is player ' . $winner . '!', "\n";

			echo $prefix, "\n\n", '== Post-game results ==', "\n";
			echo $prefix, 'Player 1\'s deck: ', implode(', ', $game[1]), "\n";
			echo $prefix, 'Player 2\'s deck: ', implode(', ', $game[2]), "\n";
		}

		return [$winner, calculateScore($game[$winner])];
	}

	function recursiveCombat($game, $gameId = 1, $prefix = "\t") {
		$myGameId = $gameId++;
		$isDebug = isDebug();

		if ($isDebug) { echo $prefix, '=== Game ' . $myGameId .' ===', "\n"; }

		$previousGames = [];
		$round = 0;
		while (!empty($game[1]) && !empty($game[2])) {
			$round++;
			if ($isDebug) {
				echo "\n", $prefix, '-- Round ' . $round .' (Game ' . $myGameId .') --', "\n";
				echo $prefix, 'Player 1\'s deck: ', implode(', ', $game[1]), "\n";
				echo $prefix, 'Player 2\'s deck: ', implode(', ', $game[2]), "\n";
			}

			$enc = json_encode($game);
			if (in_array($enc, $previousGames)) {
				if ($isDebug) {
					echo $prefix, 'Instant win for player 1.', "\n";
				}
				return [1, -1, $gameId];
			}
			$previousGames[] = $enc;

			$card1 = array_shift($game[1]);
			$card2 = array_shift($game[2]);

			if ($isDebug) {
				echo $prefix, 'Player 1 plays: ', $card1, "\n";
				echo $prefix, 'Player 2 plays: ', $card2, "\n";
			}

			if (count($game[1]) >= $card1 && count($game[2]) >= $card2) {
				// NEW GAME
				if ($isDebug) {
					echo $prefix, 'Playing a sub-game to determine the winner...', "\n\n";
				}
				$subGame = ['1' => array_slice($game[1], 0, $card1),
				            '2' => array_slice($game[2], 0, $card2)];
				[$winner, $_, $gameId] = recursiveCombat($subGame, $gameId, $prefix . "\t");
				if ($isDebug) {
					echo "\n", $prefix, '...anyway, back to game ' . $myGameId . '.', "\n";
				}
			} else {
				$winner = ($card1 > $card2) ? 1 : 2;
			}

			if ($isDebug) {
				echo $prefix, 'Player ' . $winner . ' wins round ' . $round .' of game ' . $myGameId .'!', "\n";
			}

			if ($winner == 1) {
				$game[1] = array_merge($game[1], [$card1, $card2]);
			} else {
				$game[2] = array_merge($game[2], [$card2, $card1]);
			}
		}

		$winner = empty($game[1]) ? 2 : 1;

		if ($isDebug) {
			echo $prefix, 'The winner of game ' . $myGameId . ' is player ' . $winner . '!', "\n";

			if ($myGameId == 1) {
				echo "\n\n", $prefix, '== Post-game results ==', "\n";
				echo $prefix, 'Player 1\'s deck: ', implode(', ', $game[1]), "\n";
				echo $prefix, 'Player 2\'s deck: ', implode(', ', $game[2]), "\n";
			}
		}

		return [$winner, calculateScore($game[$winner]), $gameId];
	}

	[$winner, $score] = combat($players);
	echo 'Part 1: ', $score, "\n";

	[$winner, $score] = recursiveCombat($players);
	echo 'Part 2: ', $score, "\n";
