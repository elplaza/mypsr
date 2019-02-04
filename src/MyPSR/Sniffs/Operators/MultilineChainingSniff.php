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
		$this->setFile($phpcsFile);

		$soc = $this->chainingStart($stackPtr);
		$eoc = $this->chainingEnd($stackPtr);
		if (
			$this->areValid(array($soc, $eoc))
			&& !$this->isSameLine($soc, $eoc)
		) {
			if ($this->isInArray($stackPtr)) {
				$this->noWhitespaceBefore($stackPtr);
			} else {
				$this->startCodeOfLine($stackPtr);
			}
		}
	}

}
