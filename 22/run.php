#!/usr/bin/php
<?php
    $__CLI['long'] = ['players:'];
    $__CLI['extrahelp'] = [];
    $__CLI['extrahelp'][] = '      --players <#>        Reshuffle the deck for <#> players (https://www.reddit.com/r/adventofcode/comments/ki4jy3)';

	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$players = [];
	foreach ($input as $group) {
		$name = array_shift($group);
		preg_match('#Player (.*):#SADi', $name, $m);
		$name = $m[1];
		$players[$name] = array_map('intval', $group);
	}

	function calculateScore($deck) {
		$score = 0;
		for ($i = 0; $i < count($deck); $i++) {
			$score += (count($deck) - $i) * $deck[$i];
		}

		return $score;
	}

	function combat($game, $recursive, $gameId = 1, $prefix = "\t") {
		$myGameId = $gameId++;
		$isDebug = isDebug();

		if ($isDebug) { echo $prefix, '=== Game', ($recursive ? ' ' . $myGameId : '') , ' ===', "\n"; }

		$previousGames = [];
		$round = 0;
		while (count($game) > 1) {
			$round++;
			if ($isDebug) {
				echo "\n", $prefix, '-- Round ' , $round , ($recursive ? ' (Game ' . $myGameId .')' : ''), ' --', "\n";
				foreach ($game as $pid => $deck) {
					echo $prefix, 'Player ', $pid, '\'s deck: ', implode(', ', $deck), "\n";
				}
			}

			$enc = json_encode($game);
			if (isset($previousGames[$enc])) {
				$winningPlayer = array_keys($game)[0];
				if ($isDebug) {
					echo $prefix, 'Instant win for player ', $winningPlayer, '.', "\n";
				}
				return [$winningPlayer, 0, $gameId];
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

			if ($recursive && $canRecurse) {
				// NEW GAME
				if ($isDebug) {
					echo $prefix, 'Playing a sub-game to determine the winner...', "\n\n";
				}

				$subGame = [];
				foreach ($card as $pid => $c) {
					$subGame[$pid] = array_slice($game[$pid], 0, $card[$pid]);
				}

				[$winner, $score, $gameId] = combat($subGame, $recursive, $gameId, $prefix . "\t");
				if ($isDebug) {
					echo "\n", $prefix, '...anyway, back to game ', $myGameId, '.', "\n";
				}
			} else {
				$winner = array_keys($card, max($card))[0];
			}

			if ($isDebug) {
				echo $prefix, 'Player ', $winner, ' wins round ', $round, ($recursive ? ' of game ' . $myGameId : '' ), '!', "\n";
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
			echo $prefix, 'The winner ', ($recursive ? 'of game ' . $myGameId . ' ' : ''), 'is player ', $winner, '!', "\n";

			if ($myGameId == 1) {
				echo "\n\n", $prefix, '== Post-game results ==', "\n";
				foreach ($game as $pid => $deck) {
					echo $prefix, 'Player ', $pid, '\'s deck: ', implode(', ', $deck), "\n";
				}
			}
		}

		return [$winner, calculateScore($game[$winner]), $gameId];
	}

	function redistributeCards($game, $playerCount) {
		$players = array_fill(1, $playerCount, []);

		$i = 0;
		foreach ($game as $deck) {
			foreach ($deck as $c) {
				$players[($i++ % $playerCount) + 1][] = $c;
			}
		}

		return $players;
	}

	if (isset($__CLIOPTS['players']) && is_numeric($__CLIOPTS['players'])) {
		$players = redistributeCards($players, $__CLIOPTS['players']);
	}

	[$winner, $score] = combat($players, false);
	echo 'Part 1 - Player ', $winner, ' won with: ', $score, "\n";

	[$winner, $score] = combat($players, true);
	echo 'Part 2 - Player ', $winner, ' won with: ', $score, "\n";
