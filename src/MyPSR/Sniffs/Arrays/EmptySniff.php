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
		$this->setFile($phpcsFile);

		// parentesi di apertura e di chiusura dell'array
		$open  = $this->getArrayOpenParenthesis($stackPtr);
		$close = $this->getArrayCloseParenthesis($stackPtr);

		if (($open + 1) === $close) {
			return;
		}

		if ($this->isEmptyArray($stackPtr)) {
			$error = "Empty array must have close parenthesis after open parenthesis";
			$fix   = $this->file->addFixableError($error, $close, "EmptyArray");
			if ($fix === true) {
				$this->fixer->beginChangeset();
				for ($i = ($open + 1); $i < $close; $i++) {
					$this->fixer->replaceToken($i, "");
				}
				$this->fixer->endChangeset();
			}
		}
	}

}
