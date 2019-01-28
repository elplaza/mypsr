<?php

namespace MyPSR\Sniffs\Files;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

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
        // processo tutti i token da $stackPtr in poi
        for ($i = $stackPtr; $i < $phpcsFile->numTokens; $i++) {
            // considero riga per riga
            if ($this->isSol($phpcsFile, $i)) {
                // se la riga è più lunga del massimo
                // consentito provo a splittarla
                $length = $this->getLineLength($phpcsFile, $i);
                if ($length > $this->maxLineLength) {
                    $this->splitLine($phpcsFile, $i);
                }
            }
        }
    }

    protected function splitLine(File $phpcsFile, $ptr)
    {
        $sol = $this->findSol($phpcsFile, $ptr);
        $eol = $this->findEol($phpcsFile, $ptr);

        if (is_null($sol) || is_null($eol)) {
            return;
        }

        // sostituisce la riga composta solo da whitespaces con l'eolChar
        $onlyWhitespace = $this->replaceWhitespaceLine($phpcsFile, $sol, $eol);

        if (empty($onlyWhitespace)) {
            // "splitta" le righe composte solo da commenti (ed eventualmente spazi)
            $commentLine = $this->splitCommentLine($phpcsFile, $sol, $eol);
            if (empty($commentLine)) {
                // "splitta" le righe con del codice dentro
                $ecol = $this->findEcol($phpcsFile, $eol);
                if ($this->isValid($phpcsFile, $ecol)) {
                    $phpcsFile->addError(
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
     * @param  File $phpcsFile
     * @param  int  $sol start-of-line
     * @param  int  $eol end-of-line
     * @return void
     */
    protected function splitCommentLine(File $phpcsFile, $sol, $eol)
    {
        $tokens = $phpcsFile->getTokens();

        if ($this->isSameLine($phpcsFile, $sol, $eol)) {
            // se la riga contiene codice allora esco
            $ecol = $this->findEcol($phpcsFile, $sol, $eol);
            if (!is_null($ecol) && $this->isSameLine($phpcsFile, $ecol, $eol)) {
                return false;
            }

            $lastComment = $this->prevComment($phpcsFile, $eol, $sol);
            if (
                $this->isComment($phpcsFile, $lastComment)
                && $this->isSameLine($phpcsFile, $lastComment, $eol)
            ) {
                if ($this->isComment($phpcsFile, $lastComment, "oneline")) {
                    $lcu = $this->getUnits($phpcsFile, $lastComment);
                    if ($lcu >= ($this->maxLineLength - 2)) {
                        $fix = $phpcsFile->addFixableError(
                            "This line is greater than {$this->maxLineLength} chars. "
                            . "Move the comment in new line",
                            $sol,
                            "SplitCommentLine"
                        );
                        if ($fix === true) {
                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->addNewlineBefore($lastComment);
                            $phpcsFile->fixer->endChangeset();
                        }
                    } else {
                        $commentMaxLength = $this->maxLineLength - $lcu - 2;
                        $fix = $phpcsFile->addFixableError(
                            "This line is greater than {$this->maxLineLength} chars. "
                            . "Split the comment in more lines",
                            $sol,
                            "SplitCommentLine"
                        );

                        if ($fix === true) {
                            $content    = $tokens[$lastComment]["content"];
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

                            $contentB      = str_replace("\t", str_repeat(" ", $this->getTabWidth($phpcsFile)), $content);
                            $contentA      = ltrim($contentB);
                            $spaces        = strlen($contentB) - strlen($contentA);
                            $length        = $tokens[$lastComment]["column"] - 1 + $spaces;
                            $tabs          = intval($length / $this->getTabWidth($phpcsFile));
                            $newLength     = $length - $tabs * ($this->getTabWidth($phpcsFile) - 1);
                            $beforeContent = str_pad(str_repeat("\t", $tabs), $newLength);

                            $newContent = wordwrap(
                                $tokens[$lastComment]["content"],
                                $commentMaxLength,
                                $phpcsFile->eolChar . $beforeContent . $symbol,
                                true
                            );

                            $phpcsFile->fixer->beginChangeset();
                            $phpcsFile->fixer->replaceToken($lastComment, $newContent);
                            $phpcsFile->fixer->endChangeset();
                        }
                    }

                    return true;
                } elseif ($this->isComment($phpcsFile, $lastComment, "multiline")) {
                    $fix = $phpcsFile->addFixableError(
                        "This line is greater than {$this->maxLineLength} chars. "
                        . "Split the comment in more lines",
                        $sol,
                        "SplitCommentLine"
                    );
                    if ($fix === true) {
                        $string        = $phpcsFile->findPrevious(array(T_DOC_COMMENT_STRING), $lastComment, $sol);
                        $starComment   = $phpcsFile->findPrevious(array(T_DOC_COMMENT_OPEN_TAG), $string);
                        $length        = $tokens[$starComment]["column"];
                        $tabs          = intval($length / $this->getTabWidth($phpcsFile));
                        $newLength     = $length - $tabs * ($this->getTabWidth($phpcsFile) - 1);
                        $indentation   = str_pad(str_repeat("\t", $tabs), $newLength);
                        $untilStar     = $indentation . "*";
                        $toFill        = $tokens[$string]["column"] - $length - 2;
                        $beforeContent = $untilStar . str_repeat(" ", $toFill);

                        $commentMaxLength = $this->maxLineLength - $tokens[$string]["column"] - 3;

                        $newContent = wordwrap(
                            $tokens[$string]["content"],
                            $commentMaxLength,
                            $phpcsFile->eolChar . $beforeContent,
                            true
                        );

                        $phpcsFile->fixer->beginChangeset();
                        $phpcsFile->fixer->replaceToken($string, $newContent);
                        $phpcsFile->fixer->endChangeset();
                    }

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Sostituisce le righe composte solo da spazi con l'eolChar
     * @param  File $phpcsFile
     * @param  int  $sol start-of-line
     * @param  int  $eol end-of-line
     * @return void
     */
    protected function replaceWhitespaceLine(File $phpcsFile, $sol, $eol)
    {
        $onlyWhitespace = false;
        if ($this->isSameLine($phpcsFile, $sol, $eol)) {
            $onlyWhitespace = true;
            for ($i = $sol; $i <= $eol; $i++) {
                if (!$this->isWhitespace($phpcsFile, $i)) {
                    $onlyWhitespace = false;
                }
            }

            if ($onlyWhitespace) {
                $fix = $phpcsFile->addFixableError(
                    "This line is greater than {$this->maxLineLength} chars. "
                    . "It's composed only by whitespaces",
                    $sol,
                    "SplitWhitespaceLine"
                );
                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $sol; $i <= $eol; $i++) {
                        $phpcsFile->fixer->replaceToken($i, "");
                    }
                    $phpcsFile->fixer->addNewline($sol - 1);
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }

        return $onlyWhitespace;
    }

}
