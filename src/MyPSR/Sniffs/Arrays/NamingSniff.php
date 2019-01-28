<?php

namespace MyPSR\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;

/**
 * Sniff to ensure that arrays conform to the array naming conventions.
 */
class NamingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
    use \MyPSR\Sniffs\UtilityTrait;

    public function register()
    {
        return array(T_ARRAY);
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // la keyword Array dovrebbe essere in minuscolo.
        $this->toLowercase($phpcsFile, $stackPtr);

        // la parentesi di apertura dovrebbe stare subito dopo la keyword array
        $open = $this->getArrayOpenParenthesis($phpcsFile, $stackPtr);
        if ($open !== ($stackPtr + 1)) {
            $error = "There must be no space between the \"array\""
                . " keyword and the opening parenthesis"
            ;

            $fix = $phpcsFile->addFixableError(
                $error,
                $stackPtr,
                "SpaceAfterKeyword"
            );

            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($stackPtr + 1); $i < $open; $i++) {
                    $phpcsFile->fixer->replaceToken($i, "");
                }

                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
