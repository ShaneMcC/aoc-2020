#!/usr/bin/php
<?php
    $__CLI['long'] = ['max:'];
    $__CLI['extrahelp'] = [];
    $__CLI['extrahelp'][] = '      --max <#>            Only run once, stop at <#>';

	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = explode(',', getInputLine());

	class SpokenData {
		public $spoken;
		public $diff;

		public function __construct($spoken, $diff = 0) {
			$this->spoken = $spoken;
			$this->diff = $diff;
		}
	}

	function getSpokenNumberAt($turn, $input) {
		$isDebug = isDebug();

		$spoken = [];

		foreach ($input as $i => $num) {
			$spoken[$num] = new SpokenData($i);
			if ($isDebug) { echo sprintf('Turn %6s', ($i + 1)), "\n\t", 'Spoke starter number: ', $num, "\n"; }
		}

		for ($i = count($input); $i < $turn; $i++) {
			if ($isDebug) {
				$prev = $spoken[$num];

				echo sprintf('Turn %6s', ($i + 1)), "\n";
				echo "\t", 'Considering ', $num, ' previously spoken at ', ($prev->spoken + 1), ($prev->diff > 0 ? ' and ' . (($prev->spoken - $prev->diff) + 1) : ''), "\n";
				echo "\t", 'Spoke number: ', $prev->diff, "\n";

				$num = $prev->diff;
			} else {
				$num = $spoken[$num]->diff;
			}

			if (!isset($spoken[$num])) {
				$spoken[$num] = new SpokenData($i);
			} else {
				$spoken[$num]->diff = abs($spoken[$num]->spoken - $i);
				$spoken[$num]->spoken = $i;
			}
		}

		return $num;
	}

	if (isset($__CLIOPTS['max']) && is_numeric($__CLIOPTS['max'])) {
		$num = $__CLIOPTS['max'];
		$ans = getSpokenNumberAt($num, $input);
		echo 'Answer at ' . $num . ': ', $ans, "\n";
		die();
	}

	$part1 = getSpokenNumberAt(2020, $input);
	echo 'Part 1: ', $part1, "\n";

	$part2 = getSpokenNumberAt(30000000, $input);
	echo 'Part 2: ', $part2, "\n";
