<?php

	$encodedChars[5][6] = ['011001001010010111101001010010' => 'A',
	                       '111001001011100100101001011100' => 'B',
	                       '011001001010000100001001001100' => 'C',
	                       '' => 'D',
	                       '111101000011100100001000011110' => 'E',
	                       '111101000011100100001000010000' => 'F',
	                       '011001001010000101101001001110' => 'G',
	                       '100101001011110100101001010010' => 'H',
	                       '' => 'I',
	                       '001100001000010000101001001100' => 'J',
	                       '100101010011000101001010010010' => 'K',
	                       '100001000010000100001000011110' => 'L',
	                       '' => 'M',
	                       '' => 'N',
	                       '' => 'O',
	                       '111001001010010111001000010000' => 'P',
	                       '' => 'Q',
	                       '111001001010010111001010010010' => 'R',
	                       '' => 'S',
	                       '' => 'T',
	                       '100101001010010100101001001100' => 'U',
	                       '' => 'V',
	                       '' => 'W',
	                       '' => 'X',
	                       '100011000101010001000010000100' => 'Y',
	                       '111100001000100010001000011110' => 'Z',
	                       '000000000000000000000000000000' => ' ',
	                      ];


	function decodeText($image, $width = 5, $height = 6) {
		global $encodedChars;

		$text = '';
		$charCount = floor((is_array($image[0]) ? count($image[0]) : strlen($image[0])) / $width);
		$chars = [];

		if (!isset($encodedChars[$width][$height])) { return str_repeat('?', $charCount);  }
		$encChars = $encodedChars[$width][$height];

		foreach ($image as $row) {
			for ($i = 0; $i < $charCount; $i++) {
				$chars[$i][] = is_array($row) ? implode('', array_slice($row, ($i * 5), 5)) : substr($row, ($i * 5), 5);
			}
		}

		foreach ($chars as $c) {
			$id = implode('', $c);
			if (isDebug() && !isset($encChars[$id])) { echo 'Unknown Letter: ', $id, "\n"; }
			$text .= isset($encChars[$id]) ? $encChars[$id] : '?';
		}

		return $text;
	}
