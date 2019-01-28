<?php

namespace MyPSR\Sniffs\Methods;

use PHP_CodeSniffer\Files\File;

/**
 * Tutti i metodi devono avere la visibilità esplicitata
 */
class VisibilitySniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
    use \MyPSR\Sniffs\UtilityTrait;

    public function register()
    {
        return array(T_FUNCTION);
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if ($methodName === null) {
            // Ignora le closure
            return;
        }

        if ($phpcsFile->hasCondition($stackPtr, T_FUNCTION) === true) {
            // Ignora le funzioni innestate
            return;
        }

        if ($phpcsFile->hasCondition($stackPtr, array(T_CLASS, T_ANON_CLASS, T_TRAIT)) === false) {
            // Ignora le funzioni globali
            return;
        }

        $properties = $phpcsFile->getMethodProperties($stackPtr);
        if ($properties["scope_specified"]) {
            return;
        } else {
            $error = sprintf("Visibility must be declared on method \"%s\"", $methodName);
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, "MissingVisibility");
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addContentBefore($stackPtr, "public ");
                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
