<?php

namespace MyPSR\Sniffs\WhiteSpace;

/**
 * Manda a capo e indenta il punto e virgola
 */
class SemicolonSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return array(T_SEMICOLON);
	}

	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
	{
		$sos = $phpcsFile->findStartOfStatement($stackPtr - 1);

		// inline
		if ($this->isSameLine($phpcsFile, $stackPtr, $sos)) {
			$this->noWhitespaceBefore($phpcsFile, $stackPtr);
			return;
		}

		// dopo la parentesi tonda di chiusura
		$prevCode = $this->prevCode($phpcsFile, $stackPtr - 1, $sos);
		if (
			$this->isCloseBracket($phpcsFile, $prevCode)
			&& $this->isScol($phpcsFile, $prevCode)
			&& $this->isSameLine($phpcsFile, $prevCode, $stackPtr)
		) {
			$this->noWhitespaceBefore($phpcsFile, $stackPtr);
			return;
		}

		// multiline e non subito dopo la parentesi di chiusura
		if ($this->isScol($phpcsFile, $stackPtr)) {
			$iSos = $this->getWhitespaces($phpcsFile, $sos);
			$this->checkIndentation($phpcsFile, $stackPtr, $iSos);
		} else {
			$this->startCodeOfLine($phpcsFile, $stackPtr);
		}
	}

}
