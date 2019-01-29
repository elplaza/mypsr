<?php

namespace MyPSR\Sniffs\Arrays;

/**
 * Gli array single line devono:
 * - non avere una virgola dopo l'ultimo elemento
 * - non avere spazi prima delle virgole e un solo spazio dopo
 * - avere uno spazio prima e dopo le =>
 * - non avere spazi o commenti dopo la parentesi di apertura
 *   e prima della parentesi di chiusura
 */
class SingleLineSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return $this->getArrays();
	}

	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
	{
		if (
			!$this->isEmptyArray($phpcsFile, $stackPtr)
			&& $this->isSingleLineArray($phpcsFile, $stackPtr)
		) {
			// levo la spazzatura (fondamentalmente spazi) tra
			// la parentesi di apertura e il primo valore
			$open = $this->getArrayOpenParenthesis($phpcsFile, $stackPtr);
			$this->noWhitespaceAfter($phpcsFile, $open);

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
			$close    = $this->getArrayCloseParenthesis($phpcsFile, $stackPtr);
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
	}
}
