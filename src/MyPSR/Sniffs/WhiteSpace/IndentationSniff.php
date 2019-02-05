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
		$this->setFile($phpcsFile);

		// controllo che l'indentazione del token T_OPEN_TAG sia 0
		// così fissiamo il punto di riferimento per tutti i token
		// successivi
		$iOpenTag = $this->getUnits($stackPtr);
		if ($iOpenTag !== 0) {
			$content = $this->tokens[$stackPtr]["content"];

			$fix = $this->file->addFixableError(
				"\"%s\" is not indented correctly",
				$stackPtr,
				"IncorrectIndentation",
				array($content)
			);

			if ($fix === true) {
				$this->fixer->beginChangeset();
				if ($stackPtr === 0) {
					$this->fixer->replaceToken($stackPtr, ltrim($content));
				} else {
					for ($i = 0; $i < $stackPtr; $i++) {
						$this->fixer->replaceToken($i, "");
					}
				}

				$this->fixer->endChangeset();
			}
		}


		// parso tutti i start-code-of-line e:
		// @todo qui me ne frego dei commenti, ma sarebbe da
		//       estendere anche ai commenti
		for ($i = $stackPtr + 1; $i < $this->file->numTokens; $i++) {
			if ($this->isScol($i)) {

				if ($this->isInParenthesis($i)) {
					if ($this->isObjectOperator($i) || $this->isTernary($i)) {
						continue;
					}
				}

				// l'indentazione del token $i di base sarà
				// quella del prev-start-code-of-line (PSCOL).
				$prevScol = $this->prevScol($i);
				$iPscol   = $this->getWhitespaces($prevScol);

				$prevEcol = $this->prevEcol($i);

				// può cambiare in casi specifici:
				if ($this->isCloseBracket($i)) {
					// se è una parentesi di chiusura multiline, la indento
					// con la stessa indentazione della parentesi di apertura
					$opener  = $this->getOpener($i);
					$iOpener = $this->getWhitespaces($opener);

					$this->checkIndentation($i, $iOpener);
				} elseif ($this->isSwitchKeyword($i)) {
					// se è un "case" o un "default", lo indento
					// di 1 tab rispetto allo switch
					$switch = $this->file->findPrevious(array(T_SWITCH), $i - 1);
					if ($switch !== false) {
						$iSwitch = $this->getWhitespaces($switch);
						$this->checkIndentation($i, $iSwitch, 1);
					}
				} elseif ($this->isOperator($i) && !$this->isOperator($prevScol)) {
					// se è un operatore e il PSCOL non lo è,
					// allora lo indento di 1 tab in più
					$this->checkIndentation($i, $iPscol, 1);
				} elseif ($this->isBooleanOperator($i) && !$this->isBooleanOperator($prevScol)) {
					// se è un operatore booleano e il PSCOL non lo è,
					// allora lo indento di 1 tab in più
					$this->checkIndentation($i, $iPscol);
				} elseif (
					$this->isObjectOperator($i)
					&& $this->isFirstChaining($i)
					&& !$this->isInParenthesis($i)
				) {
					// se è il primo operatore di chaining
					// allora lo indento di 1 tab in più
					$this->checkIndentation($i, $iPscol, 1);
				} elseif (
					$this->isConcatOperator($i)
					&& !$this->isConcatOperator($prevScol)
					&& !$this->isString($prevScol)
				) {
					// se è un operatore di concatenazione di stringa
					// e il PSCOL non lo è, allora lo indento di 1 tab in più
					$this->checkIndentation($i, $iPscol, 1);
				} elseif ($this->isTernary($i)) {
					// se è l'operatore ternario
					$this->checkIndentation($i, $iPscol, 1);
				} elseif ($this->isSemicolon($i)) {
					// se è un ";" devo andarmi a prendere lo start-of-statement
					// e indentarla come il SCOL dello SOS
					$sos = $this->file->findStartOfStatement($i - 1);
					if (!empty($sos) && !$this->isNoCode($sos)) {
						$sosScol = $this->findScol($sos);
						if (!is_null($sosScol)) {
							$iSosScol = $this->getWhitespaces($sosScol);
							$this->checkIndentation($i, $iSosScol);
						}
					}
				} else {
					// se non è uno dei casi precedenti, allora:
					// prendo il PECOL: prev-end-code-of-line
					if ($this->isBlockOpener($prevEcol) || $this->isColon($prevEcol)) {
						// se il PECOL è una parentesi di apertura multiline
						// o un ":" allora indento IPSCOL + 1tab
						$this->checkIndentation($i, $iPscol, 1);
					} else {
						// in tutti gli altri casi l'indentazione
						// è quella del PSCOL
						$this->checkIndentation($i, $iPscol);
					}
				}
			}
		}
	}

}
