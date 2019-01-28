<?php

namespace MyPSR\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

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

	public function process(File $phpcsFile, $stackPtr)
	{
        $sos = $phpcsFile->findStartOfStatement($stackPtr);
        $eos = $phpcsFile->findEndOfStatement($stackPtr);
        if (
            $this->isValid($phpcsFile, $sos)
            && $this->isValid($phpcsFile, $eos)
            && !$this->isSameLine($phpcsFile, $sos, $eos)
        ) {
            $this->startCodeOfLine($phpcsFile, $stackPtr);
        }
	}
}
