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

				$passport[$kvbit[0]] = $kvbit[1];
			}
		}
	}

	if (count($passport) > 0) { $passports[] = $passport; }

	$fields = [];
	$fields['byr'] = true;
	$fields['iyr'] = true;
	$fields['eyr'] = true;
	$fields['hgt'] = true;
	$fields['hcl'] = true;
	$fields['ecl'] = true;
	$fields['pid'] = true;
	$fields['cid'] = false;
	$part1 = 0;

	foreach ($passports as $p) {
		$valid = true;

		foreach ($fields as $key => $required) {
			if ($required && !isset($p[$key])) {
				$valid = false;
				break;
			}
		}

		if ($valid) { $part1++; }
	}


	echo 'Part 1: ', $part1, "\n";
