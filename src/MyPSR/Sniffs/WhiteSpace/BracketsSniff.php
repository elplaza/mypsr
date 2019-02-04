<?php

namespace MyPSR\Sniffs\WhiteSpace;

/**
 * Processa tutte le parentesi:
 * - se sono in "single-line" allora leva gli spazi
 *   dopo quella di apertura e prima di quella di chiusura
 * - se sono in "multi-line" allora manda a capo dopo
 *   quella di apertura e impone che quella di chiusura
 *   sia uno start-code-of-line
 */
class BracketsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return $this->getBlockOpeners();
	}

	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
	{
		$this->setFile($phpcsFile);

		$closer = $this->getCloser($stackPtr);
		if (!is_null($closer)) {
			if ($this->isSameLine($stackPtr, $closer)) {
				$this->noWhitespaceAfter($stackPtr);
				$this->noWhitespaceBefore($closer);
			} else {
				$this->oneEolAfter($stackPtr);
				$this->startCodeOfLine($closer);
			}
		}
	}

}
