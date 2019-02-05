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
		return $this->getTernary();
	}

	public function process(File $phpcsFile, $stackPtr)
	{
		$this->setFile($phpcsFile);

		$start = $this->file->findStartOfStatement($stackPtr);
		$end   = $this->file->findEndOfStatement($stackPtr);
		$else  = $this->findTernaryElse($stackPtr);

		if ($this->isSameLine($start, $end) || $this->isInParenthesis($stackPtr)) {
			$this->oneSpaceAround($stackPtr);
			$this->oneSpaceAround($else);
		} else {
			$this->startCodeOfLine($stackPtr);
			$this->startCodeOfLine($else);

			$this->oneSpaceAfter($stackPtr);
			$this->oneSpaceAfter($else);

			$this->removeEmptyLines($start, $end);
		}
	}

	private function findTernaryElse($stackPtr)
	{
		$start  = $stackPtr + 1;
		$eos    = $this->file->findEndOfStatement($stackPtr);
		do {
			$code = $this->nextCode($start, $eos);
			if (!empty($code)) {
				if ($this->tokens[$code]["code"] === T_OPEN_PARENTHESIS) {
					$start = $this->tokens[$code]["parenthesis_closer"] + 1;
				} else {
					$start = $code + 1;
				}
			}
		} while (!empty($code) && $this->tokens[$code]["code"] !== T_INLINE_ELSE);

		return ($code <= $eos) ? $code : $eos;
	}

}
