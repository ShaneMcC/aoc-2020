#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$part1 = 0;

	$entries = [];
	foreach ($input as $line) {
		$bits = str_split($line);

		$RowMin = 0;
		$RowMax = 128;

		$ColMin = 0;
		$ColMax = 8;

		foreach ($bits as $bit) {
			if ($bit == 'F') {
				$RowMax -= ($RowMax - $RowMin) / 2;
				echo $bit . ' changed row range to: ', $RowMax, ' - ',  $RowMax, "\n";
			} else if ($bit == 'B') {
				$RowMin += ($RowMax - $RowMin) / 2;
				echo $bit . ' changed row range to: ', $RowMax, ' - ',  $RowMax, "\n";
			} else if ($bit == 'L') {
				$ColMax -= ($ColMax - $ColMin) / 2;
				echo $bit . ' changed col range to: ', $ColMin, ' - ',  $ColMax, "\n";
			} else if ($bit == 'R') {
				$ColMin += ($ColMax - $ColMin) / 2;
				echo $bit . ' changed col range to: ', $ColMin, ' - ',  $ColMax, "\n";
			}
		}

		$seatId = $RowMin * 8 + $ColMin;

		$entries[] = ['data' => $line, 'row' => $RowMin, 'col' => $ColMin, 'seatId' => $seatId];

		$part1 = max($seatId, $part1);
	}

	var_dump($entries);

	echo $part1, "\n";
