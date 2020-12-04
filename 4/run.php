#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = explode("\n", getInputContent());

	$passports = [];

	$passport = [];

	foreach ($input as $line) {
		if (empty($line)) {
			if (count($passport) > 0) { $passports[] = $passport; }
			$passport = [];
		} else {
			$bits = explode(' ', $line);

			foreach ($bits as $bit) {
				$kvbit = explode(':', $bit, 2);

				$passport[$kvbit[0]] = ['val' => $kvbit[1]];
			}
		}
	}

	if (count($passport) > 0) { $passports[] = $passport; }

	$fields = [];
	$fields['byr'] = ['required' => true,
	                  'validation' => [['min' => 1920, 'max' => 2002]],
	                 ];

	$fields['iyr'] = ['required' => true,
	                  'validation' => [['min' => 2010, 'max' => 2020]],
	                 ];

	$fields['eyr'] = ['required' => true,
	                  'validation' => [['min' => 2020, 'max' => 2030]],
	                 ];

	$fields['hgt'] = ['required' => true,
	                  'validation' => [['regex' => '#^([0-9]{3})cm#i', 'min' => 150, 'max' => 193],
	                                   ['regex' => '#^([0-9]{2})in#i', 'min' => 59, 'max' => 76],
	                                  ],
	                 ];

	$fields['hcl'] = ['required' => true,
	                  'validation' => [['regex' => '/^#[0-9a-f]{6}$/i']],
	                 ];

	$fields['ecl'] = ['required' => true,
	                  'validation' => [['regex' => '#^(amb|blu|brn|gry|grn|hzl|oth)$#i']],
	                 ];

	$fields['pid'] = ['required' => true,
	                  'validation' => [['regex' => '#^[0-9]{9}$#i']],
	                 ];

	$fields['cid'] = ['required' => false];


	$part1 = $part2 = 0;

	function validateField($val, $validator) {
		if (isset($validator['regex'])) {

			 if (preg_match($validator['regex'], $val, $m)) {
			 	if (isset($m[1])) { $val = $m[1]; } // Future matches should be on the matched value.
			 } else {
			 	return false;
			 }

		}

		if (isset($validator['min']) && $val < $validator['min']) { return false; }
		if (isset($validator['max']) && $val > $validator['max']) { return false; }

		return true;
	}

	foreach ($passports as $p) {
		$allFields = true;
		$validated = true;

		$failedReasons = [];

		foreach ($fields as $key => $info) {
			if ($info['required'] && !isset($p[$key])) {
				$allFields = false;
			}

			if (isset($p[$key]) && isset($info['validation'])) {
				$fieldValid = false;
				foreach ($info['validation'] as $v) {
					if (validateField($p[$key]['val'], $v)) {
						$fieldValid = true;
						break;
					}
				}

				if (!$fieldValid) { $validated = false; }
			}
		}

		if ($allFields) { $part1++; }
		if ($allFields && $validated) { $part2++; }
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
