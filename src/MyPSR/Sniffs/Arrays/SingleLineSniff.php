<?php

namespace MyPSR\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;

/**
 * Sniff to ensure that single line arrays conform to the array coding standard.
 */
class SingleLineSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return $this->getArrays();
	}

	public function process(File $phpcsFile, $stackPtr)
	{
		if (
			!$this->isEmptyArray($phpcsFile, $stackPtr)
			&& $this->isSingleLineArray($phpcsFile, $stackPtr)
		) {
			// levo la spazzatura (fondamentalmente spazi) tra
			// la parentesi di apertura e il primo valore
			$open      = $this->getArrayOpenParenthesis($phpcsFile, $stackPtr);
			$close     = $this->getArrayCloseParenthesis($phpcsFile, $stackPtr);
			$firstCode = $this->nextCode($phpcsFile, $open + 1, $close);
			if ($firstCode !== $open + 1) {
				$error = "The code must be immediately after the opening bracket in one value array declaration";
				$fix   = $phpcsFile->addFixableError($error, $firstCode, "TrashAfterOpen");
				if ($fix === true) {
					$phpcsFile->fixer->beginChangeset();
					for ($i = $open + 1; $i < $firstCode; $i++) {
						$phpcsFile->fixer->replaceToken($i, "");
					}
					$phpcsFile->fixer->endChangeset();
				}
			}

			// processo tutte le virgole "valide"
			$commas = $this->getArrayValidCommas($phpcsFile, $stackPtr);
			if (!empty($commas)) {
				foreach ($commas as $comma) {
					// prima di ogni virgola "valida" non ci devono essere spazi
					$this->noWhitespaceBefore($phpcsFile, $comma);
					// dopo la virgola ci dev'essere uno e un solo spazio
					$this->oneSpaceAfter($phpcsFile, $comma);
				}
			}

			// processo tutte le doppie frecce "valide"
			$arrows = $this->getArrayValidDoubleArrows($phpcsFile, $stackPtr);
			if (!empty($arrows)) {
				foreach ($arrows as $arrow) {
					// prima e dopo ogni freccia ci dev'essere uno e un solo spazio
					$this->oneSpaceAround($phpcsFile, $arrow);
				}
			}

			// levo gli spazi e la virgola "opzionale" tra
			// l'ultimo valore e la parentesi di chiusura
			$lastCode = $this->prevCode($phpcsFile, $close - 1, $open);
			if ($this->isComma($phpcsFile, $lastCode)) {
				$lastCode = $this->prevCode($phpcsFile, $lastCode - 1, $open);
			}

			if ($lastCode !== $close - 1) {
				$error = "The code must be immediately before the closing bracket in one value array declaration";
				$fix   = $phpcsFile->addFixableError($error, $lastCode, "TrashBeforeClose");
				if ($fix === true) {
					$phpcsFile->fixer->beginChangeset();
					for ($i = $lastCode + 1; $i < $close; $i++) {
						$phpcsFile->fixer->replaceToken($i, "");
					}
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}//end process()
}//end class
