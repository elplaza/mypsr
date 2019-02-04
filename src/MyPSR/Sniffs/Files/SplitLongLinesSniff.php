<?php

namespace MyPSR\Sniffs\Files;

use PHP_CodeSniffer\Files\File;

/**
 * Processa tutte le righe più lunghe di un tot e prova a splittarle
 */
class SplitLongLinesSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public $maxLineLength = 100;

	public function register()
	{
		return array(T_OPEN_TAG);
	}

	public function process(File $phpcsFile, $stackPtr)
	{
		$this->setFile($phpcsFile);

		// processo tutti i token da $stackPtr in poi
		for ($i = $stackPtr; $i < $this->file->numTokens; $i++) {
			// considero riga per riga
			if ($this->isSol($i)) {
				// se la riga è più lunga del massimo
				// consentito provo a splittarla
				$length = $this->getLineLength($i);
				if ($length > $this->maxLineLength) {
					$this->splitLine($i);
				}
			}
		}
	}

	protected function splitLine($ptr)
	{
		$sol = $this->findSol($ptr);
		$eol = $this->findEol($ptr);

		if (is_null($sol) || is_null($eol)) {
			return;
		}

		// sostituisce la riga composta solo da whitespaces con l'eolChar
		$onlyWhitespace = $this->replaceWhitespaceLine($sol, $eol);

		if (empty($onlyWhitespace)) {
			// "splitta" le righe composte solo da commenti (ed eventualmente spazi)
			$commentLine = $this->splitCommentLine($sol, $eol);
			if (empty($commentLine)) {
				// "splitta" le righe con del codice dentro
				$ecol = $this->findEcol($eol);
				if ($this->isValid($ecol)) {
					$this->file->addError(
						"This line is greater than {$this->maxLineLength} chars.",
						$ecol,
						"SplitCodeLine"
					);
				}
			}
		}
	}

	/**
	 * Splitta le righe composte solo da commenti
	 * ed eventualmente da whitespaces
	 * @param  int  $sol start-of-line
	 * @param  int  $eol end-of-line
	 * @return void
	 */
	protected function splitCommentLine($sol, $eol)
	{
		if ($this->isSameLine($sol, $eol)) {
			// se la riga contiene codice allora esco
			$ecol = $this->findEcol($sol, $eol);
			if (!is_null($ecol) && $this->isSameLine($ecol, $eol)) {
				return false;
			}

			$lastComment = $this->prevComment($eol, $sol);
			if (
				$this->isComment($lastComment)
				&& $this->isSameLine($lastComment, $eol)
			) {
				if ($this->isComment($lastComment, "oneline")) {
					$lcu = $this->getUnits($lastComment);
					if ($lcu >= ($this->maxLineLength - 2)) {
						$fix = $this->file->addFixableError(
							"This line is greater than {$this->maxLineLength} chars. "
							. "Move the comment in new line",
							$sol,
							"SplitCommentLine"
						);
						if ($fix === true) {
							$this->fixer->beginChangeset();
							$this->fixer->addNewlineBefore($lastComment);
							$this->fixer->endChangeset();
						}
					} else {
						$commentMaxLength = $this->maxLineLength - $lcu - 2;
						$fix = $this->file->addFixableError(
							"This line is greater than {$this->maxLineLength} chars. "
							. "Split the comment in more lines",
							$sol,
							"SplitCommentLine"
						);

						if ($fix === true) {
							$content    = $this->tokens[$lastComment]["content"];
							$firstChars = substr($content, 0, 2);
							if ($firstChars[0] == "#") {
								$symbol = "# ";
							} elseif ($firstChars == "//") {
								$symbol = "//";
							} elseif ($firstChars == "/*") {
								$symbol = "  ";
							} else {
								$symbol = "";
							}

							$contentB      = str_replace("\t", str_repeat(" ", $this->getTabWidth()), $content);
							$contentA      = ltrim($contentB);
							$spaces        = strlen($contentB) - strlen($contentA);
							$length        = $this->tokens[$lastComment]["column"] - 1 + $spaces;
							$tabs          = intval($length / $this->getTabWidth());
							$newLength     = $length - $tabs * ($this->getTabWidth() - 1);
							$beforeContent = str_pad(str_repeat("\t", $tabs), $newLength);

							$newContent = wordwrap(
								$this->tokens[$lastComment]["content"],
								$commentMaxLength,
								$this->file->eolChar . $beforeContent . $symbol,
								true
							);

							$this->fixer->beginChangeset();
							$this->fixer->replaceToken($lastComment, $newContent);
							$this->fixer->endChangeset();
						}
					}

					return true;
				} elseif ($this->isComment($lastComment, "multiline")) {
					$fix = $this->file->addFixableError(
						"This line is greater than {$this->maxLineLength} chars. "
						. "Split the comment in more lines",
						$sol,
						"SplitCommentLine"
					);
					if ($fix === true) {
						$string        = $this->file->findPrevious(array(T_DOC_COMMENT_STRING), $lastComment, $sol);
						$starComment   = $this->file->findPrevious(array(T_DOC_COMMENT_OPEN_TAG), $string);
						$length        = $this->tokens[$starComment]["column"];
						$tabs          = intval($length / $this->getTabWidth());
						$newLength     = $length - $tabs * ($this->getTabWidth() - 1);
						$indentation   = str_pad(str_repeat("\t", $tabs), $newLength);
						$untilStar     = $indentation . "*";
						$toFill        = $this->tokens[$string]["column"] - $length - 2;
						$beforeContent = $untilStar . str_repeat(" ", $toFill);

						$commentMaxLength = $this->maxLineLength - $this->tokens[$string]["column"] - 3;

						$newContent = wordwrap(
							$this->tokens[$string]["content"],
							$commentMaxLength,
							$this->file->eolChar . $beforeContent,
							true
						);

						$this->fixer->beginChangeset();
						$this->fixer->replaceToken($string, $newContent);
						$this->fixer->endChangeset();
					}

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Sostituisce le righe composte solo da spazi con l'eolChar
	 * @param  int  $sol start-of-line
	 * @param  int  $eol end-of-line
	 * @return void
	 */
	protected function replaceWhitespaceLine($sol, $eol)
	{
		$onlyWhitespace = false;
		if ($this->isSameLine($sol, $eol)) {
			$onlyWhitespace = true;
			for ($i = $sol; $i <= $eol; $i++) {
				if (!$this->isWhitespace($i)) {
					$onlyWhitespace = false;
				}
			}

			if ($onlyWhitespace) {
				$fix = $this->file->addFixableError(
					"This line is greater than {$this->maxLineLength} chars. "
					. "It's composed only by whitespaces",
					$sol,
					"SplitWhitespaceLine"
				);
				if ($fix === true) {
					$this->fixer->beginChangeset();
					for ($i = $sol; $i <= $eol; $i++) {
						$this->fixer->replaceToken($i, "");
					}
					$this->fixer->addNewline($sol - 1);
					$this->fixer->endChangeset();
				}
			}
		}

		return $onlyWhitespace;
	}

}
