#!/usr/bin/php
<?php
	$__CLI['long'] = ['output', 'fast', 'ascii', 'asciiin', 'asciiout', 'nodebug', 'in0:', 'in1:', 'in2:', 'out0'];
	$__CLI['extrahelp'] = [];
	$__CLI['extrahelp'][] = '      --in0 <val>          Set <value> into mem[0]';
	$__CLI['extrahelp'][] = '      --in1 <val>          Set <value> into mem[1]';
	$__CLI['extrahelp'][] = '      --in2 <val>          Set <value> into mem[2]';
	$__CLI['extrahelp'][] = '      --out0               Show the final value at mem[0]';
	$__CLI['extrahelp'][] = '      --output             Show all output at the end.';
	$__CLI['extrahelp'][] = '      --ascii              Assume input and output are ascii';
	$__CLI['extrahelp'][] = '      --asciiout           Assume output is ascii';
	$__CLI['extrahelp'][] = '      --asciiin            Convert input from ascii';
	$__CLI['extrahelp'][] = '      --fast               Remove sleep time on debug.';
	$__CLI['extrahelp'][] = '      --nodebug            No debug, only output.';

	require_once(dirname(__FILE__) . '/common.php');
	require_once(dirname(__FILE__) . '/IntCodeVM.php');
	$input = getInputLine();

	$vm = new IntCodeVM(IntCodeVM::parseInstrLines($input));
	$vm->useInterrupts(true);
	if (!isset($__CLIOPTS['nodebug'])) {
		$vm->setDebug(true);
		if (isset($__CLIOPTS['fast'])) {
			$vm->setDebug(true, 0);
		}
	}

	if (isset($__CLIOPTS['ascii'])) {
		$__CLIOPTS['asciiin'] = $__CLIOPTS['asciiout'] = $__CLIOPTS['ascii'];
	}

	for ($i = 0; $i <= 2; $i++) {
		$name = 'in' . $i;
		if (isset($__CLIOPTS[$name])) {
			$val = is_array($__CLIOPTS[$name]) ? $__CLIOPTS[$name][count($__CLIOPTS[$name]) - 1] : $__CLIOPTS[$name];
			$vm->setData($i, $val);
		}
	}

	$output = [];

	while (!$vm->hasExited()) {
		try {
			$vm->step();
			if ($vm->hasExited()) { break; }
		} catch (OutputGivenInterrupt $ex) {
			$out = $vm->getOutput();
			$output[] = $out;
			if (isset($__CLIOPTS['asciiout'])) {
				echo chr($out);
			} else {
				echo 'Output: ', $out, "\n";
			}
		} catch (InputWantedException $ex) {
			// Take user input.
			$in = [];
			if (isset($__CLIOPTS['asciiin'])) {
				foreach (str_split(readline('Input: ')) as $i) { $in[] = ord($i); }
				$in[] = ord("\n");
			} else {
				while (true) {
					$i = readline('Input Number: ');
					if (is_numeric($i)) { $in[] = $i; break; }
					echo 'ERROR: Input must be numeric.', "\n";
				}
			}
			foreach ($in as $i) { $vm->appendInput($i); }
		}
	}
	echo 'End.', "\n\n";

	if (isset($__CLIOPTS['out0'])) {
		echo 'Output at 0: ', $vm->getData(0), "\n";
	}

	if (isset($__CLIOPTS['output'])) {
		echo 'All output: ', "\n";
		if (isset($__CLIOPTS['ascii'])) {
			foreach ($output as $o) { echo chr($o); }
		} else {
			echo implode(', ', $output);
		}

		echo "\n";
	}
