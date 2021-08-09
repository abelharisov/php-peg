<?php

namespace hafriedlander\Peg\Compiler\Token;

use hafriedlander\Peg\Compiler\PHPBuilder;

class Literal extends Expressionable {

	function __construct($value) {
		parent::__construct('literal', "'" . \mb_substr($value,1,-1) . "'");
	}

	function match_code($value) {
		try {
			$evald = eval('return '. $value . ';');
		} catch (\ParseError $e) {
			die("PEG grammar parsing error in >return $value;<': " . $e->getMessage());
		}

		// We inline single-character matches for speed
		if (!$this->contains_expression($value) && \mb_strlen($evald) === 1) {
			return $this->match_fail_conditional('\mb_substr($this->string, $this->pos, 1) === '.$value,
				PHPBuilder::build()->l(
					'$this->pos += 1;',
					$this->set_text($value)
				)
			);
		}
		return parent::match_code($value);
	}
}
