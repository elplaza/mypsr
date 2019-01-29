<?php

namespace MyPSR\Sniffs\Operators;

/**
 * Splitta i chaining multiline
 */
class MultilineChainingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return array(T_OBJECT_OPERATOR);
	}

	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
	{
		$soc = $this->chainingStart($phpcsFile, $stackPtr);
		$eoc = $this->chainingEnd($phpcsFile, $stackPtr);
		if (
			$this->areValid($phpcsFile, array($soc, $eoc))
			&& !$this->isSameLine($phpcsFile, $soc, $eoc)
		) {
			$this->startCodeOfLine($phpcsFile, $stackPtr);
		}
	}
}
