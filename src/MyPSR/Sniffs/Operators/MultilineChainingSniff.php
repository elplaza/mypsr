<?php

namespace MyPSR\Sniffs\Operators;

use PHP_CodeSniffer\Files\File;

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

	public function process(File $phpcsFile, $stackPtr)
	{
		$soc = $this->chainingStart($phpcsFile, $stackPtr);
		$eoc = $this->chainingEnd($phpcsFile, $stackPtr);
		if (
			$this->isValid($phpcsFile, $soc)
			&& $this->isValid($phpcsFile, $eoc)
			&& !$this->isSameLine($phpcsFile, $soc, $eoc)
		) {
			$this->startCodeOfLine($phpcsFile, $stackPtr);
		}
	}

	// restituisce l'inizio di tutto il chaining
	private function chainingStart(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			do {
				$start = $this->startChain($phpcsFile, $ptr);
				$ptr   = $this->prevChain($phpcsFile, $ptr);
			} while ($this->isObjectOperator($phpcsFile, $ptr));

			return $start;
		}
	}

	// restituisce la fine di tutto il chaining
	private function chainingEnd(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			do {
				$end = $this->endChain($phpcsFile, $ptr);
				$ptr = $this->nextChain($phpcsFile, $ptr);
			} while ($this->isObjectOperator($phpcsFile, $ptr));

			if ($this->isCloseBracket($phpcsFile, $end)) {
				return $this->getOpener($phpcsFile, $end);
			}

			return $end;
		}
	}

	// restituisce il token che apre il chain di $ptr
	private function startChain(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			$sos    = $phpcsFile->findStartOfStatement($ptr);
			$prev1  = $this->prevCode($phpcsFile, $ptr - 1, $sos);
			if ($this->isCloseBracket($phpcsFile, $prev1)) {
				$opener = $this->getOpener($phpcsFile, $prev1);
				$prev1  = $this->prevCode($phpcsFile, $opener - 1, $sos);
			}

			$tokens = $phpcsFile->getTokens();
			return in_array($tokens[$prev1]["code"], array(T_STRING, T_VARIABLE)) ? $prev1 : $sos;
		}
	}

	// restituisce il prev operator di chaining
	private function prevChain(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			$startPtrChain = $this->startChain($phpcsFile, $ptr);
			if ($this->isValid($phpcsFile, $startPtrChain)) {
				$sos     = $phpcsFile->findStartOfStatement($ptr);
				$prevOpr = $this->prevCode($phpcsFile, $startPtrChain - 1, $sos);
				if ($this->isObjectOperator($phpcsFile, $prevOpr)) {
					return $prevOpr;
				}
			}
		}
	}

	// restituisce il token che chiude il chain di $ptr
	private function endChain(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			$eos    = $phpcsFile->findEndOfStatement($ptr);
			$next1  = $this->nextCode($phpcsFile, $ptr + 1, $eos);
			$tokens = $phpcsFile->getTokens();
			if (in_array($tokens[$next1]["code"], array(T_STRING, T_VARIABLE))) {
				$next2 = $this->nextCode($phpcsFile, $next1 + 1, $eos);
				if ($this->isBlockOpener($phpcsFile, $next2)) {
					return $this->getCloser($phpcsFile, $next2);
				} else {
					return $next1;
				}
			}
		}
	}

	// restituisce il next operator di chaining
	private function nextChain(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			$endPtrChain = $this->endChain($phpcsFile, $ptr);
			if ($this->isValid($phpcsFile, $endPtrChain)) {
				$eos     = $phpcsFile->findEndOfStatement($ptr);
				$nextOpr = $this->nextCode($phpcsFile, $endPtrChain + 1, $eos);
				if ($this->isObjectOperator($phpcsFile, $nextOpr)) {
					return $nextOpr;
				}
			}
		}
	}

}
