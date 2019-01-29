<?php

namespace MyPSR\Sniffs\Arrays;

/**
 * La keyword array dev'essere in minuscolo e la parentesi
 * di apertura deve stare subito dopo la keyword
 */
class NamingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
    use \MyPSR\Sniffs\UtilityTrait;

    public function register()
    {
        return array(T_ARRAY);
    }

    public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
    {
        // la keyword Array dovrebbe essere in minuscolo.
        $this->toLowercase($phpcsFile, $stackPtr);

        // la parentesi di apertura dovrebbe stare subito dopo la keyword array
        $this->noWhitespaceAfter($phpcsFile, $stackPtr);
    }
}
