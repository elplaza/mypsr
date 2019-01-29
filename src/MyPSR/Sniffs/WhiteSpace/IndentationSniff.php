<?php

namespace MyPSR\Sniffs\WhiteSpace;

/**
 * Indenta tutti i first-of-line
 *
 * Nota: questo sniff si occupa solo di indentare tutti i first-of-line,
 * altri sniff si devono preoccupare di rendere qualcosa first-of-line
 */
class IndentationSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return array(T_OPEN_TAG);
	}

	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

		// controllo che l'indentazione del token T_OPEN_TAG sia 0
		// così fissiamo il punto di riferimento per tutti i token
		// successivi
		$iOpenTag = $this->getUnits($phpcsFile, $stackPtr);
		if ($iOpenTag !== 0) {
			$content = $tokens[$stackPtr]["content"];

			$fix = $phpcsFile->addFixableError(
				"\"%s\" is not indented correctly",
				$stackPtr,
				"IncorrectIndentation",
				array($content)
			);

			if ($fix === true) {
				$phpcsFile->fixer->beginChangeset();
				if ($stackPtr === 0) {
					$phpcsFile->fixer->replaceToken($stackPtr, ltrim($content));
				} else {
					for ($i = 0; $i < $stackPtr; $i++) {
						$phpcsFile->fixer->replaceToken($i, "");
					}
				}

				$phpcsFile->fixer->endChangeset();
			}
		}

		// parso tutti i start-code-of-line e:
		// @todo qui me ne frego dei commenti, ma sarebbe da
		//       estendere anche ai commenti
		$output = array();
		for ($i = $stackPtr + 1; $i < $phpcsFile->numTokens; $i++) {
			if ($this->isScol($phpcsFile, $i)) {
				$iIndentation = $this->getWhitespaces($phpcsFile, $i);

				// l'indentazione del token $i di base sarà
				// quella del prev-start-code-of-line (PSCOL).
				$prevScol = $this->prevScol($phpcsFile, $i);
				$iPscol   = $this->getWhitespaces($phpcsFile, $prevScol);

				$prevEcol = $this->prevEcol($phpcsFile, $i);

				// può cambiare in casi specifici:
				if ($this->isCloseBracket($phpcsFile, $i)) {
					// se è una parentesi di chiusura multiline, la indento
					// con la stessa indentazione della parentesi di apertura
					$opener  = $this->getOpener($phpcsFile, $i);
					$iOpener = $this->getWhitespaces($phpcsFile, $opener);

					$this->checkIndentation($phpcsFile, $i, $iOpener);

				} elseif ($this->isSwitchKeyword($phpcsFile, $i)) {
					// se è un "case" o un "default", lo indento
					// di 1 tab rispetto allo switch
					$switch = $phpcsFile->findPrevious(array(T_SWITCH), $i - 1);
					if ($switch !== false) {
						$iSwitch = $this->getWhitespaces($phpcsFile, $switch);
						$this->checkIndentation($phpcsFile, $i, $iSwitch, 1);
					}
				} elseif (
					$this->isOperator($phpcsFile, $i)
					&& !$this->isOperator($phpcsFile, $prevScol)
				) {
					// se è un operatore e il PSCOL non lo è,
					// allora lo indento di 1 tab in più
					$this->checkIndentation($phpcsFile, $i, $iPscol, 1);
				} elseif (
					$this->isBooleanOperator($phpcsFile, $i)
					&& !$this->isBooleanOperator($phpcsFile, $prevScol)
				) {
					// se è un operatore booleano e il PSCOL non lo è,
					// allora lo indento di 1 tab in più
					$this->checkIndentation($phpcsFile, $i, $iPscol);
				} elseif (
					$this->isObjectOperator($phpcsFile, $i)
					&& $this->isFirstChaining($phpcsFile, $i)
				) {
					// se è il primo operatore di chaining
					// allora lo indento di 1 tab in più
					$this->checkIndentation($phpcsFile, $i, $iPscol, 1);
				} elseif (
					$this->isConcatOperator($phpcsFile, $i)
					&& !$this->isConcatOperator($phpcsFile, $prevScol)
					&& !$this->isString($phpcsFile, $prevScol)
				) {
					// se è un operatore di concatenazione di stringa
					// e il PSCOL non lo è, allora lo indento di 1 tab in più
					$this->checkIndentation($phpcsFile, $i, $iPscol, 1);
				} elseif ($this->isTernary($phpcsFile, $i)) {
					// se è l'operatore ternario
					$this->checkIndentation($phpcsFile, $i, $iPscol, 1);
				} elseif ($this->isSemicolon($phpcsFile, $i)) {
					// se è un ";" devo andarmi a prendere lo start-of-statement
					// e indentarla come il SCOL dello SOS
					$sos = $phpcsFile->findStartOfStatement($i - 1);
					if (!empty($sos) && !$this->isNoCode($phpcsFile, $sos)) {
						$sosScol = $this->findScol($phpcsFile, $sos);
						if (!is_null($sosScol)) {
							$iSosScol = $this->getWhitespaces($phpcsFile, $sosScol);
							$this->checkIndentation($phpcsFile, $i, $iSosScol);
						}
					}
				} else {
					// se non è uno dei casi precedenti, allora:
					// prendo il PECOL: prev-end-code-of-line
					if (
						$this->isBlockOpener($phpcsFile, $prevEcol)
						|| $this->isColon($phpcsFile, $prevEcol)
					) {
						// se il PECOL è una parentesi di apertura multiline
						// o un ":" allora indento IPSCOL + 1tab
						$this->checkIndentation($phpcsFile, $i, $iPscol, 1);
					} else {
						// in tutti gli altri casi l'indentazione
						// è quella del PSCOL
						$this->checkIndentation($phpcsFile, $i, $iPscol);
					}
				}
			}
		}
	}

}
