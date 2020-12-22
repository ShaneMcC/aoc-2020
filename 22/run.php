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

		while (count($game) > 1) {
			$round++;
			if ($isDebug) {
				echo "\n", $prefix, '-- Round ' . $round .' --', "\n";
				foreach ($game as $pid => $deck) {
					echo $prefix, 'Player ', $pid, '\'s deck: ', implode(', ', $deck), "\n";
				}
			}

			$card = [];
			foreach ($game as $pid => $deck) { $card[$pid] = array_shift($game[$pid]); }
			$winner = array_keys($card, max($card))[0];

			if ($isDebug) {
				foreach ($card as $pid => $c) {
					echo $prefix, 'Player ', $pid, ' plays: ', $c, "\n";
				}
				echo $prefix, 'Player ' . $winner . ' wins the round!', "\n";
			}

			$winnerCard = [$card[$winner]];
			unset($card[$winner]);
			$game[$winner] = array_merge($game[$winner], $winnerCard, $card);

			foreach ($card as $pid => $c) {
				if (empty($game[$pid])) { unset($game[$pid]); }
			}
		}

		$winner = array_keys($game)[0];

		if ($isDebug) {
			echo $prefix, 'The winner is player ' . $winner . '!', "\n";

			echo $prefix, "\n\n", '== Post-game results ==', "\n";
			foreach ($game as $pid => $deck) {
				echo $prefix, 'Player ', $pid, '\'s deck: ', implode(', ', $deck), "\n";
			}
		}

		return [$winner, calculateScore($game[$winner])];
	}

	function recursiveCombat($game, $gameId = 1, $prefix = "\t") {
		$myGameId = $gameId++;
		$isDebug = isDebug();

		if ($isDebug) { echo $prefix, '=== Game ' . $myGameId .' ===', "\n"; }

		$previousGames = [];
		$round = 0;
		while (count($game) > 1) {
			$round++;
			if ($isDebug) {
				echo "\n", $prefix, '-- Round ' . $round .' (Game ' . $myGameId .') --', "\n";
				foreach ($game as $pid => $deck) {
					echo $prefix, 'Player ', $pid, '\'s deck: ', implode(', ', $deck), "\n";
				}
			}

			$enc = json_encode($game);
			if (isset($previousGames[$enc])) {
				if ($isDebug) {
					echo $prefix, 'Instant win for player 1.', "\n";
				}
				return [1, 0, $gameId];
			}
			$previousGames[$enc] = true;

			$card = [];
			$canRecurse = true;
			foreach ($game as $pid => $deck) {
				$card[$pid] = array_shift($game[$pid]);
				$canRecurse = $canRecurse && count($game[$pid]) >= $card[$pid];
			}

			if ($isDebug) {
				foreach ($card as $pid => $c) {
					echo $prefix, 'Player ', $pid, ' plays: ', $c, "\n";
				}
			}

			if ($canRecurse) {
				// NEW GAME
				if ($isDebug) {
					echo $prefix, 'Playing a sub-game to determine the winner...', "\n\n";
				}

				$subGame = [];
				foreach ($card as $pid => $c) {
					$subGame[$pid] = array_slice($game[$pid], 0, $card[$pid]);
				}

				[$winner, $score, $gameId] = recursiveCombat($subGame, $gameId, $prefix . "\t");
				if ($isDebug) {
					echo "\n", $prefix, '...anyway, back to game ' . $myGameId . '.', "\n";
				}
			} else {
				$winner = array_keys($card, max($card))[0];
			}

			if ($isDebug) {
				echo $prefix, 'Player ' . $winner . ' wins round ' . $round .' of game ' . $myGameId .'!', "\n";
			}

			$winnerCard = [$card[$winner]];
			unset($card[$winner]);
			$game[$winner] = array_merge($game[$winner], $winnerCard, $card);

			foreach ($card as $pid => $c) {
				if (empty($game[$pid])) { unset($game[$pid]); }
			}
		}

		$winner = array_keys($game)[0];

		if ($isDebug) {
			echo $prefix, 'The winner of game ' . $myGameId . ' is player ' . $winner . '!', "\n";

			if ($myGameId == 1) {
				echo "\n\n", $prefix, '== Post-game results ==', "\n";
				foreach ($game as $pid => $deck) {
					echo $prefix, 'Player ', $pid, '\'s deck: ', implode(', ', $deck), "\n";
				}
			}
		}

		return [$winner, calculateScore($game[$winner]), $gameId];
	}

	[$winner, $score] = combat($players);
	echo 'Part 1: ', $score, "\n";

	[$winner, $score] = recursiveCombat($players);
	echo 'Part 2: ', $score, "\n";
