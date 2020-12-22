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
		$part1 += ($v * $game[$winner][$i - 1]);
	}

	echo 'Part 1: ', $part1, "\n";

	$gameId = 1;
	$knownGames = [];
	function recursiveCombat($game) {
		global $gameId, $knownGames;

		$myGameId = $gameId++;

		if (isDebug()) { echo '=== Game ' . $myGameId .' ===', "\n"; }

		$startEnc = json_encode($game);
		if (isset($knownGames[$startEnc])) {
			if (isDebug()) {
				echo 'Game has known winner: ', $knownGames[$startEnc][0], "\n";
			}
			return $knownGames[$startEnc];
		}

		$previousGames = [];
		$round = 0;
		while (!empty($game[1]) && !empty($game[2])) {
			$round++;
			if (isDebug()) {
				echo "\n", '-- Round ' . $round .' (Game ' . $myGameId .') --', "\n";
				echo 'Player 1\'s deck: ', implode(', ', $game[1]), "\n";
				echo 'Player 2\'s deck: ', implode(', ', $game[2]), "\n";
			}

			$enc = json_encode($game);
			if (in_array($enc, $previousGames)) {
				if (isDebug()) {
					echo 'Instant win for player 1.', "\n";
				}
				$knownGames[$startEnc] = [1, $game];
				return [1, $game];
			}
			$previousGames[] = $enc;

			$card1 = array_shift($game[1]);
			$card2 = array_shift($game[2]);

			if (isDebug()) {
				echo 'Player 1 plays: ', $card1, "\n";
				echo 'Player 2 plays: ', $card2, "\n";
			}

			if (count($game[1]) >= $card1 && count($game[2]) >= $card2) {
				// NEW GAME
				if (isDebug()) {
					echo 'Playing a sub-game to determine the winner...', "\n";
				}
				$subGame = [];
				$subGame[1] = array_slice($game[1], 0, $card1);
				$subGame[2] = array_slice($game[2], 0, $card2);
				[$winner, $_] = recursiveCombat($subGame);
				if (isDebug()) {
					echo '...anyway, back to game ' . $myGameId . '.', "\n";
				}
			} else {
				$winner = ($card1 > $card2) ? 1 : 2;
			}

			if (isDebug()) {
				echo 'Player ' . $winner . ' wins round ' . $round .' of game ' . $myGameId .'!', "\n";
			}

			if ($winner == 1) {
				$game[1][] = $card1;
				$game[1][] = $card2;
			} else {
				$game[2][] = $card2;
				$game[2][] = $card1;
			}
		}

		$winner = empty($game[1]) ? 2 : 1;

		if (isDebug()) {
			echo 'The winner of game ' . $myGameId . ' is player ' . $winner . '!', "\n";
		}

		$knownGames[$startEnc] = [$winner, $game];
		return [$winner, $game];
	}

	$game = $players;
	[$winner, $game] = recursiveCombat($game);

	$part1 = 0;
	for ($i = 1; $i <= count($game[$winner]); $i++) {
		$v = (count($game[$winner]) - $i) + 1;
		$part1 += ($v * $game[$winner][$i - 1]);
	}

	echo 'Part 2: ', $part1, "\n";
