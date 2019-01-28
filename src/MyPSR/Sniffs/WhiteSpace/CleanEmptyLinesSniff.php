<?php

namespace MyPSR\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class CleanEmptyLinesSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return array(T_OPEN_PARENTHESIS);
	}

	/**
	 * Processa tutte le parentesi tonde e rimuove le righe vuote
	 */
	public function process(File $phpcsFile, $stackPtr)
	{
		$closer = $this->getCloser($phpcsFile, $stackPtr);
		if (!is_null($closer)) {
			$this->removeEmptyLines($phpcsFile, $stackPtr, $closer);
		}
	}

}
