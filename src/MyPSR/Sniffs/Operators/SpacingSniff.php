<?php

namespace MyPSR\Sniffs\Operators;

/**
 * Gli operatori devono essere seguite e precedute da uno e un solo spazio
 */
class SpacingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return array_merge(
			$this->getOperators(),
			$this->getBooleanOperators(),
			$this->getEquality(),
			$this->getAssignment(),
			array(T_STRING_CONCAT)
		);
	}

	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
	{
		if ($this->isDoubleArrow($phpcsFile, $stackPtr)) {
			return;
		}

		$sos = $phpcsFile->findStartOfStatement($stackPtr);
		if ($this->isValid($phpcsFile, $sos)) {
			if ($sos === $stackPtr) {
				$this->noWhitespaceAfter($phpcsFile, $stackPtr);
			} else {
				$scol = $this->prevCode($phpcsFile, $stackPtr - 1, $sos);
				if (!$this->isArithmeticOperator($phpcsFile, $stackPtr)) {
					$this->oneSpaceAfter($phpcsFile, $stackPtr);
				} else {
					if (
						$this->isAssignment($phpcsFile, $scol)
						|| $this->isComma($phpcsFile, $scol)
						|| $this->isBlockOpener($phpcsFile, $scol)
					) {
						$this->noWhitespaceAfter($phpcsFile, $stackPtr);
					} else {
						$this->oneSpaceAfter($phpcsFile, $stackPtr);
					}
				}

				if (
					!$this->isAssignment($phpcsFile, $stackPtr)
					&& $this->isSameLine($phpcsFile, $scol, $stackPtr)
				) {
					$this->oneSpaceBefore($phpcsFile, $stackPtr);
				}
			}
		}
	}

}
