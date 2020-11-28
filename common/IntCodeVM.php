<?php

	require_once(dirname(__FILE__) . '/../common/VM.php');

	/**
	 * Simple IntCode VM
	 */
	class IntCodeVM extends VM {
		/**
		 * Init the opcodes.
		 */
		protected function init() {
			/**
			 * add
			 *   - 1 X Y Z
			 *
			 * sets memory position Z to the value of memory position X + memory position Y.
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 * @param $modes Parameter modes.
			 */
			$this->instrs['1'] = ['ADD', 3, function($vm, $args, $modes = []) {
				[$x, $y, $z] = $args;

				$xMode = $modes[0];
				$yMode = $modes[1];
				$zMode = $modes[2];

				return $vm->setData($z, ($vm->getData($x, $xMode) + $vm->getData($y, $yMode)), $zMode);
			}];

			/**
			 * mul
			 *   - 2 X Y Z
			 *
			 * sets memory position Z to the value of memory position X * memory position Y.
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 * @param $modes Parameter modes.
			 */
			$this->instrs['2'] = ['MUL', 3, function($vm, $args, $modes = []) {
				[$x, $y, $z] = $args;

				$xMode = $modes[0];
				$yMode = $modes[1];
				$zMode = $modes[2];

				return $vm->setData($z, ($vm->getData($x, $xMode) * $vm->getData($y, $yMode)), $zMode);
			}];

			/**
			 * INPUT
			 *   - 3 Z
			 *
			 * sets memory position Z to the value from the input.
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 * @param $modes Parameter modes.
			 */
			$this->instrs['3'] = ['INPUT', 1, function($vm, $args, $modes = []) {
				$vm->wantsInput = true;

				[$z] = $args;
				$zMode = $modes[0];

				$input = $vm->getInput();
				if ($input !== NULL) {
					$vm->wantsInput = false;
					return $vm->setData($z, $input, $zMode);
				} else if ($vm->inputErrorInterrupt) {
					throw new InputWantedException('No Input Available');
				}
			}];

			/**
			 * OUTPUT
			 *   - 4 Z
			 *
			 * Outputs memory position Z
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 * @param $modes Parameter modes.
			 */
			$this->instrs['4'] = ['OUTPUT', 1, function($vm, $args, $modes = []) {
				[$z] = $args;

				$zMode = $modes[0];

				$r = $vm->appendOutput($vm->getData($z, $zMode));
				if ($vm->outputInterrupt) { throw new OutputGivenInterrupt(); }
				return $r;
			}];


			/**
			 * JMPTRUE
			 *   - 5 X Y
			 *
			 * if the first parameter is non-zero, it sets the instruction
			 * pointer to the value from the second parameter. Otherwise, it
			 * does nothing.
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 * @param $modes Parameter modes.
			 */
			$this->instrs['5'] = ['JMPTRUE', 2, function($vm, $args, $modes = []) {
				[$x, $y] = $args;

				$xMode = $modes[0];
				$yMode = $modes[1];

				if ($vm->getData($x, $xMode) != 0) {
					return $vm->jump($vm->getData($y, $yMode));
				}
			}];

			/**
			 * JMPFALSE
			 *   - 6 X Y
			 *
			 * if the first parameter is zero, it sets the instruction pointer
			 * to the value from the second parameter. Otherwise, it does
			 * nothing.
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 * @param $modes Parameter modes.
			 */
			$this->instrs['6'] = ['JMPFALSE', 2, function($vm, $args, $modes = []) {
				[$x, $y] = $args;

				$xMode = $modes[0];
				$yMode = $modes[1];

				if ($vm->getData($x, $xMode) == 0) {
					return $vm->jump($vm->getData($y, $yMode));
				}
			}];

			/**
			 * LESSTHAN
			 *   - 7 X Y Z
			 *
			 * if the first parameter is less than the second parameter, it
			 * stores 1 in the position given by the third parameter.
			 * Otherwise, it stores 0.
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 * @param $modes Parameter modes.
			 */
			$this->instrs['7'] = ['LESSTHAN', 3, function($vm, $args, $modes = []) {
				[$x, $y, $z] = $args;

				$xMode = $modes[0];
				$yMode = $modes[1];
				$zMode = $modes[2];

				if ($vm->getData($x, $xMode) < $vm->getData($y, $yMode)) {
					return $vm->setData($z, 1, $zMode);
				} else {
					return $vm->setData($z, 0, $zMode);
				}
			}];

			/**
			 * EQUALS
			 *   - 8 X Y Z
			 *
			 * if the first parameter is equal to the second parameter, it
			 * stores 1 in the position given by the third parameter.
			 * Otherwise, it stores 0.
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 * @param $modes Parameter modes.
			 */
			$this->instrs['8'] = ['EQUALS', 3, function($vm, $args, $modes = []) {
				[$x, $y, $z] = $args;

				$xMode = $modes[0];
				$yMode = $modes[1];
				$zMode = $modes[2];

				if ($vm->getData($x, $xMode) == $vm->getData($y, $yMode)) {
					return $vm->setData($z, 1, $zMode);
				} else {
					return $vm->setData($z, 0, $zMode);
				}
			}];

			/**
			 * ADJRELBASE
			 *   - 9 X
			 *
			 * Adjusts the relative base by the value of its only parameter.
			 * The relative base increases (or decreases, if the value is
			 * negative) by the value of the parameter.
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 * @param $modes Parameter modes.
			 */
			$this->instrs['9'] = ['ADJRELBASE', 1, function($vm, $args, $modes = []) {
				[$x] = $args;

				$xMode = $modes[0];

				return $vm->setRelativeBase($vm->getRelativeBase() + $vm->getData($x, $xMode));
			}];

			/**
			 * halt
			 *   - 99
			 *
			 * Halt.
			 *
			 * @param $vm VM to execute on.
			 * @param $args Args for this instruction.
			 */
			$this->instrs['99'] = ['HALT', 0, function($vm, $args, $modes = []) {
				return $vm->end(0);
			}];
		}

		/** Does input interrupt (Throws an OutputGivenInterrupt) */
		protected $outputInterrupt = false;

		/** Does an empty input queue throw an exception (Throws an InputWantedException) */
		protected $inputErrorInterrupt = true;

		/** Are we waiting for input? */
		protected $wantsInput = false;

		public function useInputInterrupt($value) {
			$this->inputErrorInterrupt = $value;
		}

		public function useOutputInterrupt($value) {
			$this->outputInterrupt = $value;
		}

		public function useInterrupts($value) {
			$this->useInputInterrupt($value);
			$this->useOutputInterrupt($value);
		}

		// Turn output into a queue.
		public function clearOutput() { $this->output = []; }
		public function getOutputLength() { return count($this->output); }
		public function appendOutput($value) { $this->output[] = $value; if ($this->debug) { return 'Output value: ' . $value; } }
		public function setOutput($value) { $this->output = is_array($value) ? $value : [$value]; }
		public function getOutput() { return array_shift($this->output); }
		public function getAllOutput() { return $this->output; }
		public function getOutputText() {
			$text = '';
			foreach ($this->getAllOutput() as $out) { $text .= chr($out); }
			$this->clearOutput();
			return $text;
		}

		/** Input queue for the VM. */
		protected $input = '';

		public function clearInput() { $this->input = []; }
		public function getInputLength() { return count($this->input); }
		public function appendInput($value) { $this->input[] = $value; }
		public function setInput($value) { $this->input = is_array($value) ? $value : [$value]; }
		public function getInput() { return array_shift($this->input); }
		public function getAllInput() { return $this->input; }

		public function wantsInput() { return $this->wantsInput; }

		function inputText($text, $autoNewLine = true) {
			foreach (str_split($text) as $t) { $this->appendInput(ord($t)); }
			if ($autoNewLine) { $this->appendInput(ord("\n")); }
		}

		// Relative Base
		protected $relativeBase = 0;
		public function getRelativeBase() { return $this->relativeBase; }
		public function setRelativeBase($value) { $this->relativeBase = $value; if ($this->debug) { return 'Relbase is now: ' . $value; } }

		public function clone() {
			$c = new IntCodeVM();
			$c->loadState($this->saveState());
			return $c;
		}

		public function saveState() {
			return ['in' => $this->input, 'out' => $this->output, 'loc' => $this->location, 'relbase' => $this->relativeBase, 'data' => $this->data, 'misc' => $this->miscData, 'exitCode' => $this->exitCode, 'exited' => $this->exited, 'wantsInput' => $this->wantsInput, 'interrupts' => ['in' => $this->inputErrorInterrupt, 'out' => $this->outputInterrupt]];
		}

		public function loadState($state) {
			$this->input = $state['in'];
			$this->output = $state['out'];
			$this->location = $state['loc'];
			$this->relativeBase = $state['relbase'];
			$this->data = $state['data'];
			$this->miscData = $state['misc'];
			$this->exitCode = $state['exitCode'];
			$this->exited = $state['exited'];
			$this->wantsInput = $state['wantsInput'];
			$this->inputErrorInterrupt = $state['interrupts']['in'];
			$this->outputInterrupt = $state['interrupts']['out'];
		}

		// Debugging for Jump.
		function jump($loc) {
			parent::jump($loc);
			if ($this->debug) { return 'Jumping to: ' . $loc; }
		}

		// Reset also needs to reset our new input queue not just the output
		// queue.
		function reset() {
			parent::reset();
			$this->clearInput();
		}

		/**
		 * Get the data at the given location, understanding mode parameters.
		 *
		 * @param $location Data location (or NULL for current).
		 * @param $mode Mode, 0 for position, 1 for immediate, 2 for relative.
		 * @return Data from location.
		 */
		public function getData($loc = null, $mode = 0) {
			if ($loc === null) { $loc = $this->getLocation(); }
			if ($mode == 0) {
				return isset($this->data[$loc]) ? $this->data[$loc] : 0;
			} else if ($mode == 1) {
				return $loc;
			} else if ($mode == 2) {
				return isset($this->data[$this->getRelativeBase() + $loc]) ? $this->data[$this->getRelativeBase() + $loc] : 0;
			}

			throw new BadDataLocationException('Error getting data at: ' . $loc . ' in mode: ' . $mode);
		}

		/**
		 * Set the data at the given location.
		 *
		 * @param $location Data location (or NULL for current).
		 * @param $val New Value
		 * @param $mode Mode, 0 for position, 1 is invalid, 2 for relative.
		 */
		public function setData($loc, $val, $mode = 0) {
			if ($loc === null) { $loc = $this->getLocation(); }

			if ($mode == 0) {
				$this->data[$loc] = $val;
				if ($this->debug) { return '$' . $loc . ' is now: ' . $val; }
				return;
			} else if ($mode == 2) {
				$this->data[$this->getRelativeBase() + $loc] = $val;
				if ($this->debug) { return '$' . ($this->getRelativeBase() + $loc) . ' is now: ' . $val; }
				return;
			}

			throw new BadDataLocationException('Error setting data at: ' . $loc . ' in mode: ' . $mode);
		}


		/**
		 * Step a single instruction.
		 *
		 * @return True if we executed something, else false if we have no more
		 *         to execute.
		 */
		function doStep() {
			$next = $this->data[$this->location];

			$instr = $next % 100;
			// $modes = array_reverse(str_split(substr($next, 0, -2)));
			$modes = [($next / 100) % 10,
				      ($next / 1000) % 10,
				      ($next / 10000) % 10,
				     ];

			[$name, $argCount, $ins] = $this->getInstr($instr);

			$args = array_slice($this->data, ($this->location + 1), $argCount);

			if ($this->debug) {
				if (isset($this->miscData['pid'])) { echo sprintf('[PID: %2s] ', $this->miscData['pid']); }

				$out = '';

				// Undecoded input.
				$out .= sprintf('(%4s) ', $this->location);
				$out .= sprintf('%s %s', $next, implode(' ', $args));

				$out .= str_repeat(' ', max(5, (40 - strlen($out))));

				// Decoded input.
				$out .= sprintf(' |   %10s', $name);
				for ($a = 0; $a < count($args); $a++) {
					$out .= ' ';
					$mode = isset($modes[$a]) ? $modes[$a] : 0;

					if ($mode == 0) { $out .= '$'; }
					else if ($mode == 1) { $out .= '='; }
					else if ($mode == 2) { $out .= '~'; }

					$val = $args[$a];
					if ($mode != 1) { $val .= ' (' . $this->getData($args[$a], $mode) . ')'; };

					$out .= sprintf('%-10s', $val);
				}
			}

			$this->location += $argCount;

			$ret = $ins($this, $args, $modes);

			if ($this->debug) {
				$out .= str_repeat(' ', max(5, (120 - strlen($out))));
				$out .= ' | ';

				$out .= $ret;

				echo $out, "\n";
				usleep($this->sleep);
			}

			if ($this->wantsInput) {
				// Step back to repeat the input request.
				$this->location--;
				$this->location--;
			}

			return !$this->wantsInput;
		}

		/**
		 * Parse instruction file into instruction array.
		 *
		 * @param $data Data to parse/
		 */
		public static function parseInstrLines($input) {
			return explode(',', str_replace(' ', '', $input));
		}
	}

	class IntCodeException extends VMException { }
	class InputWantedException extends IntCodeException { }
	class OutputGivenInterrupt extends IntCodeException implements VMInterrupt { }

