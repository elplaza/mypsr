<?php

namespace MyPSR\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class BracketsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return $this->getBlockOpeners();
	}

	/**
	 * Processa tutte le parentesi:
	 * - se sono in "single-line" allora leva gli spazi
	 *   dopo quella di apertura e prima di quella di chiusura
	 * - se sono in "multi-line" allora manda a capo dopo
	 *   quella di apertura e impone che quella di chiusura
	 *   sia uno start-code-of-line
	 */
	public function process(File $phpcsFile, $stackPtr)
	{
		$closer = $this->getCloser($phpcsFile, $stackPtr);
		if (!is_null($closer)) {
			if ($this->isSameLine($phpcsFile, $stackPtr, $closer)) {
				$this->noWhitespaceAfter($phpcsFile, $stackPtr);
				$this->noWhitespaceBefore($phpcsFile, $closer);
			} else {
				$this->oneEolAfter($phpcsFile, $stackPtr);
				$this->startCodeOfLine($phpcsFile, $closer);
			}
		}
	}

}
