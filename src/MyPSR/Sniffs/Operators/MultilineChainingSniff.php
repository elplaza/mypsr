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

    private function chainingStart(File $phpcsFile, $ptr = null)
    {
        if ($this->isObjectOperator($phpcsFile, $ptr)) {
            $sos   = $phpcsFile->findStartOfStatement($ptr);
            $first = $ptr;
            $open = $ptr - 1;
            do {
                $pCode = $this->prevCode($phpcsFile, $open, $sos);
                if ($this->isCloseBracket($phpcsFile, $pCode)) {
                    $open  = $this->getOpener($phpcsFile, $pCode) - 1;
                    $first = $phpcsFile->findPrevious(array(T_OBJECT_OPERATOR), $open, $sos);
                }
            } while ($this->isCloseBracket($phpcsFile, $pCode));

            if ($this->isValid($phpcsFile, $first)) {
                return $this->prevCode($phpcsFile, $first - 1, $sos);
            }

            return $sos;
        }
    }

    private function chainingEnd(File $phpcsFile, $ptr = null)
    {
        if ($this->isObjectOperator($phpcsFile, $ptr)) {
            $start  = $ptr + 1;
            $eos    = $phpcsFile->findEndOfStatement($ptr);
            $end    = $eos;
            do {
                $open  = $phpcsFile->findNext($this->getBlockOpeners(), $start, $eos);
                $close = $this->getCloser($phpcsFile, $open);
                if (!is_null($close) && $this->isValid($phpcsFile, $close + 1)) {
                    $start = $this->nextCode($phpcsFile, $close + 1, $eos);
                    $end   = $open;
                }
            } while (
                $this->isValid($phpcsFile, $close)
                && $this->isObjectOperator($phpcsFile, $start)
            );

            return $end;
        }
    }

}
