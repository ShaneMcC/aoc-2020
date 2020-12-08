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
				return $this->setAccumulator($this->getAccumulator() + $args[0]);
			};

			/**
			 * JMP <val> - Jumps to a new instruction relative to itself.
			 */
			$this->instrs['jmp'] = function($vm, $args) {
				return $vm->jump($vm->getLocation() + $args[0]);
			};

			/**
			 * NOP -Do nothing, ignores any arguments.
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
		while (true) {
			$visited[$vm->getLocation()] = true;
			if (!$vm->step()) { break; }

			if (isset($visited[$vm->getLocation()])) {
				return [FALSE, $vm->getAccumulator()];
			}
		}

		return [TRUE, $vm->getAccumulator()];
	}

	$part1 = codeHasExit($input, 1);
	echo 'Part 1: ', $part1[1], "\n";

	for ($i = 0; $i < count($input); $i++) {
		$checkCode = $input[$i][0];
		if ($checkCode == "acc") {
			continue;
		} else if ($checkCode == "nop") {
			$input[$i][0] = 'jmp';
		} else if ($checkCode == "jmp") {
			$input[$i][0] = 'nop';
		}
		$part2 = codeHasExit($input, 1);

		// Reset our change.
		$input[$i][0] = $checkCode;

		if ($part2[0] !== FALSE) {
			echo 'Part 2: ', $part2[1], "\n";
			break;
		}
	}
