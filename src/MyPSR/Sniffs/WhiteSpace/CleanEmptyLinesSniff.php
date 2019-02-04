<?php

namespace MyPSR\Sniffs\WhiteSpace;

/**
 * Processa tutte le parentesi tonde e rimuove le righe vuote
 */
class CleanEmptyLinesSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return array(T_OPEN_PARENTHESIS);
	}

	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
	{
		$this->setFile($phpcsFile);

		$closer = $this->getCloser($stackPtr);
		if (!is_null($closer)) {
			$this->removeEmptyLines($stackPtr, $closer);
		}
	}

}
