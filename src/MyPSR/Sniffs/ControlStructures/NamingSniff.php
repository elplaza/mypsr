<?php

namespace MyPSR\Sniffs\ControlStructures;

/**
 * Tutte le keyword delle struttura di controllo
 * devono essere in minuscolo
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

    public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
    {
        $this->setFile($phpcsFile);

        $this->toLowercase($stackPtr);
    }
}
