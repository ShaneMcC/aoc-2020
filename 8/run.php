#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	require_once(dirname(__FILE__) . '/../common/VM.php');
	$input = VM::parseInstrLines(getInputLines());

	class Day8VM extends VM {
		// Accumulator
		protected $accumulator = 0;
		public function getAccumulator() { return $this->accumulator; }
		public function setAccumulator($value) { $this->accumulator = $value; }

		/**
		 * Init the opcodes.
		 */
		protected function init() {
			/**
			 * ACC <val> - Increases or decreases a single global accumulator.
			 */
			$this->instrs['acc'] = function($vm, $args) {
				$vm->setAccumulator($vm->getAccumulator() + $args[0]);
				if ($vm->debug) { return 'Accumulator is now: ' . $vm->getAccumulator(); }
			};

			/**
			 * JMP <val> - Jumps to a new instruction relative to itself.
			 */
			$this->instrs['jmp'] = function($vm, $args) {
				$vm->jump($vm->getLocation() + $args[0]);
				if ($vm->debug) { return 'Jumping to: ' . $vm->getNextLocation(); } // Show the next instruction that will be run.
			};

			/**
			 * NOP - Do nothing, ignores any arguments.
			 */
			$this->instrs['nop'] = function($vm, $args) {
				return;
			};
		}
	}

	function codeHasExit($input) {
		$vm = new Day8VM($input);
		$vm->setDebug(isDebug());

		$visited = [];
		if (isDebug()) { echo '--- VM Started ---.', "\n"; }
		while (true) {
			$visited[$vm->getNextLocation()] = true;

			if (!$vm->step()) { break; }
			if (isset($visited[$vm->getNextLocation()])) {
				if (isDebug()) { echo '--- VM Looped back to instruction ' . $vm->getNextLocation() . ' ---.', "\n"; }

				return [FALSE, $vm->getAccumulator()];
			} else {

			}
		}

		if (isDebug()) { echo '--- VM Exited ---.', "\n"; }

		return [TRUE, $vm->getAccumulator()];
	}

	$part1 = codeHasExit($input, 1);
	echo 'Part 1: ', $part1[1], "\n";

	for ($i = 0; $i < count($input); $i++) {
		$changeTo = $checkCode = $input[$i][0];
		if ($checkCode == "acc") {
			continue;
		} else if ($checkCode == "nop") {
			$changeTo = 'jmp';
		} else if ($checkCode == "jmp") {
			$changeTo = 'nop';
		}

		$input[$i][0] = $changeTo;
		$part2 = codeHasExit($input, 1);
		$input[$i][0] = $checkCode;

		if ($part2[0] !== FALSE) {
			echo 'Part 2 (Changed ' . $checkCode . ' instruction at ' . $i . ' to ' . $changeTo . '): ', $part2[1], "\n";
			break;
		}
	}
