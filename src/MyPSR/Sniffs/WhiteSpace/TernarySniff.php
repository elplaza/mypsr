<?php

namespace MyPSR\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;

/**
 * Formatta l'operatore ternario
 */
class TernarySniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return array(T_INLINE_THEN);
	}

	public function process(File $phpcsFile, $stackPtr)
	{
		$start = $phpcsFile->findStartOfStatement($stackPtr);
		$end   = $phpcsFile->findEndOfStatement($stackPtr);
		$else  = $this->findTernaryElse($phpcsFile, $stackPtr);

		if ($this->isSameLine($phpcsFile, $start, $end)) {
			$this->oneSpaceAround($phpcsFile, $stackPtr);
			$this->oneSpaceAround($phpcsFile, $else);
		} else {
			$this->startCodeOfLine($phpcsFile, $stackPtr);
			$this->startCodeOfLine($phpcsFile, $else);

			$this->oneSpaceAfter($phpcsFile, $stackPtr);
			$this->oneSpaceAfter($phpcsFile, $else);

			$this->removeEmptyLines($phpcsFile, $start, $end);
		}
	}

	private function findTernaryElse(File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

		$start  = $stackPtr + 1;
		$eos    = $phpcsFile->findEndOfStatement($stackPtr);
		do {
			$code = $this->nextCode($phpcsFile, $start, $eos);
			if (!empty($code)) {
				if ($tokens[$code]["code"] === T_OPEN_PARENTHESIS) {
					$start = $tokens[$code]["parenthesis_closer"] + 1;
				} else {
					$start = $code + 1;
				}
			}
		} while (!empty($code) && $tokens[$code]["code"] !== T_INLINE_ELSE);

		return ($code <= $eos) ? $code : $eos;
	}
}
