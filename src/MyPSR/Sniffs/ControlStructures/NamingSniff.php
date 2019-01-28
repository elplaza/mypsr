<?php

namespace MyPSR\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Ensures all control structure keywords are lowercase.
 */
class NamingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
    use \MyPSR\Sniffs\UtilityTrait;

    public function register()
    {
        return array_merge(
            $this->getControStructures(),
            $this->getSwitchKeywords()
        );
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        if (!$this->isValid($phpcsFile, $stackPtr)) {
            return;
        }

        $this->toLowercase($phpcsFile, $stackPtr);
    }
}
