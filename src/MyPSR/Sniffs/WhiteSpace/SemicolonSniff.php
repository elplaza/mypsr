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
		$this->setFile($phpcsFile);

		$sos = $this->file->findStartOfStatement($stackPtr - 1);

		// inline
		if ($this->isSameLine($stackPtr, $sos)) {
			$this->noWhitespaceBefore($stackPtr);
			return;
		}

		// dopo la parentesi tonda di chiusura
		$prevCode = $this->prevCode($stackPtr - 1, $sos);
		if (
			$this->isCloseBracket($prevCode)
			&& $this->isScol($prevCode)
			&& $this->isSameLine($prevCode, $stackPtr)
		) {
			$this->noWhitespaceBefore($stackPtr);
			return;
		}

		// multiline e non subito dopo la parentesi di chiusura
		if ($this->isScol($stackPtr)) {
			$iSos = $this->getWhitespaces($sos);
			$this->checkIndentation($stackPtr, $iSos);
		} else {
			$this->startCodeOfLine($stackPtr);
		}
	}

}
