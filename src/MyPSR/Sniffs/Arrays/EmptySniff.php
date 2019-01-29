<?php

namespace MyPSR\Sniffs\Arrays;

/**
 * Gli array "vuoti" (senza codice valido all'interno)
 * devono avere le parentesi vicine
 */
class EmptySniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return $this->getArrays();
	}

	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
	{
		// parentesi di apertura e di chiusura dell'array
		$open  = $this->getArrayOpenParenthesis($phpcsFile, $stackPtr);
		$close = $this->getArrayCloseParenthesis($phpcsFile, $stackPtr);

		if (($open + 1) === $close) {
			return;
		}

		if ($this->isEmptyArray($phpcsFile, $stackPtr)) {
			$error = "Empty array must have close parenthesis after open parenthesis";
			$fix   = $phpcsFile->addFixableError($error, $close, "EmptyArray");
			if ($fix === true) {
				$phpcsFile->fixer->beginChangeset();
				for ($i = ($open + 1); $i < $close; $i++) {
					$phpcsFile->fixer->replaceToken($i, "");
				}
				$phpcsFile->fixer->endChangeset();
			}
		}
	}
}
