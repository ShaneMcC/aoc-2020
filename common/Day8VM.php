<?php

	require_once(dirname(__FILE__) . '/../common/VM.php');

	/**
	 * Simple IntCode VM
	 */
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
			$this->instrs['acc'] = ['ACC', function($vm, $args) {
				return $this->setAccumulator($this->getAccumulator() + $args[0]);
			}];

			/**
			 * JMP <val>
			 *
			 * Jumps to a new instruction relative to itself.
			 */
			$this->instrs['jmp'] = ['JUMP', function($vm, $args) {
				return $vm->jump($vm->getLocation() + $args[0]);
			}];

			/**
			 * NOP
			 *
			 * Do nothing, ignores any arguments.
			 */
			$this->instrs['nop'] = ['NOP', function($vm, $args) {
				return;
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

		// Accumulator
		protected $accumulator = 0;
		public function getAccumulator() { return $this->accumulator; }
		public function setAccumulator($value) { $this->accumulator = $value; if ($this->debug) { return 'Accumulator is now: ' . $value; } }

		public function clone() {
			$c = new IntCodeVM();
			$c->loadState($this->saveState());
			return $c;
		}

		public function saveState() {
			return ['in' => $this->input, 'out' => $this->output, 'loc' => $this->location, 'accumulator' => $this->accumulator, 'data' => $this->data, 'misc' => $this->miscData, 'exitCode' => $this->exitCode, 'exited' => $this->exited, 'wantsInput' => $this->wantsInput, 'interrupts' => ['in' => $this->inputErrorInterrupt, 'out' => $this->outputInterrupt]];
		}

		public function loadState($state) {
			$this->input = $state['in'];
			$this->output = $state['out'];
			$this->location = $state['loc'];
			$this->accumulator = $state['accumulator'];
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
			$this->setAccumulator(0);
		}

		/**
		 * Step a single instruction.
		 *
		 * @return True if we executed something, else false if we have no more
		 *         to execute.
		 */
		function doStep() {
			$next = explode(' ', $this->data[$this->location], 2);

			[$name, $ins] = $this->getInstr($next[0]);

			$args = explode(' ', $next[1]);

			if ($this->debug) {
				if (isset($this->miscData['pid'])) { echo sprintf('[PID: %2s] ', $this->miscData['pid']); }
				$out = '';

				// Undecoded input.
				$out .= sprintf('(%4s) ', $this->location);
				$out .= sprintf('%s %s', $next[0], implode(' ', $args));

				$out .= str_repeat(' ', max(5, (20 - strlen($out))));

				$out .= sprintf(' |   %10s', $name);
			}

			$ret = $ins($this, $args);

			if ($this->debug) {
				$out .= str_repeat(' ', max(5, (80 - strlen($out))));
				$out .= ' | ';

				$out .= $ret;

				echo $out, "\n";
				usleep($this->sleep);
			}

			if ($this->wantsInput) {
				// Step back to repeat the input request.
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

	class InputWantedException extends VMException { }
	class OutputGivenInterrupt extends VMException implements VMInterrupt { }

