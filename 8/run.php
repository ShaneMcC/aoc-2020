#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	require_once(dirname(__FILE__) . '/../common/VM.php');
	$input = VM::parseInstrLines(getInputLines());
	$originalInput = $input;

	class Day8VM extends VM {
		/**
		 * Init the opcodes.
		 */
		protected function init() {
			/**
			 * ACC <val>
			 *
			 * Increases or decreases a single global value called the
			 * accumulator by the value given in the argument.
			 */
			$this->instrs['acc'] = function($vm, $args) {
				return $this->setAccumulator($this->getAccumulator() + $args[0]);
			};

			/**
			 * JMP <val>
			 *
			 * Jumps to a new instruction relative to itself.
			 */
			$this->instrs['jmp'] = function($vm, $args) {
				return $vm->jump($vm->getLocation() + $args[0]);
			};

			/**
			 * NOP
			 *
			 * Do nothing, ignores any arguments.
			 */
			$this->instrs['nop'] = function($vm, $args) {
				return;
			};
		}

		// Accumulator
		protected $accumulator = 0;
		public function getAccumulator() { return $this->accumulator; }
		public function setAccumulator($value) { $this->accumulator = $value; }
	}

	function codeHasExit($input, $loopValue = 10) {
		$vm = new Day8VM($input);
		$vm->setDebug(isDebug());

		$visited = [];
		while (true) {
			if (!isset($visited[$vm->getLocation()])) { $visited[$vm->getLocation()] = 0; }
			$visited[$vm->getLocation()]++;
			if (!$vm->step()) { break; }

			if (isset($visited[$vm->getLocation()]) && $visited[$vm->getLocation()] >= $loopValue) {
				return [FALSE, $vm->getAccumulator()];
			}
		}

		return [TRUE, $vm->getAccumulator()];
	}

	$part1 = codeHasExit($input, 1);
	echo 'Part 1: ', $part1[1], "\n";

	for ($i = 0; $i < count($input); $i++) {
		$input = $originalInput;
		$line = $input[$i];

		if ($line[0] == "acc") {
			continue;
		} else if ($line[0] == "nop") {
			$input[$i][0] = 'jmp';
		} else if ($line[0] == "jmp") {
			$input[$i][0] = 'nop';
		}

		$part2 = codeHasExit($input, 1);
		if ($part2[0] !== FALSE) {
			echo 'Part 2: ', $part2[1], "\n";
			break;
		}
	}
