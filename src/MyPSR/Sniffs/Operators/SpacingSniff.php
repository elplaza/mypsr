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
		$this->setFile($phpcsFile);

		if ($this->isDoubleArrow($stackPtr)) {
			return;
		}

		$sos = $this->file->findStartOfStatement($stackPtr);
		if ($this->isValid($sos)) {
			if ($sos === $stackPtr) {
				$this->noWhitespaceAfter($stackPtr);
			} else {
				$scol = $this->prevCode($stackPtr - 1, $sos);
				if (!$this->isArithmeticOperator($stackPtr)) {
					$this->oneSpaceAfter($stackPtr);
				} else {
					if (
						$this->isAssignment($scol)
						|| $this->isComma($scol)
						|| $this->isBlockOpener($scol)
					) {
						$this->noWhitespaceAfter($stackPtr);
					} else {
						$this->oneSpaceAfter($stackPtr);
					}
				}

				if (!$this->isAssignment($stackPtr)	&& $this->isSameLine($scol, $stackPtr)) {
					$this->oneSpaceBefore($stackPtr);
				}
			}
		}
	}

}
