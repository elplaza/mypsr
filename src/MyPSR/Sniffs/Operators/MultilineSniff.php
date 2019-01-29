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
        $sos = $phpcsFile->findStartOfStatement($stackPtr);
        $eos = $phpcsFile->findEndOfStatement($stackPtr);
        if (
            $this->areValid($phpcsFile, array($sos, $eos))
            && !$this->isSameLine($phpcsFile, $sos, $eos)
        ) {
            $this->startCodeOfLine($phpcsFile, $stackPtr);
        }
	}
}
