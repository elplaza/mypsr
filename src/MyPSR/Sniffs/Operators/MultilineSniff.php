<?php

namespace MyPSR\Sniffs\Operators;

/**
 * Splitta le righe contenetnti operatori
 */
class MultilineSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return array_merge(
			$this->getOperators(),
			$this->getBooleanOperators(),
			array(T_STRING_CONCAT)
		);
	}

	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
	{
		$this->setFile($phpcsFile);

		$sos = $this->file->findStartOfStatement($stackPtr);
		$eos = $this->file->findEndOfStatement($stackPtr);
		if ($this->areValid(array($sos, $eos)) && !$this->isSameLine($sos, $eos)) {
			$this->startCodeOfLine($stackPtr);
		}
	}

}
