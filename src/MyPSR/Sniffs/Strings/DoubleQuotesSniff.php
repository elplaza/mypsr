<?php

namespace MyPSR\Sniffs\Strings;

use PHP_CodeSniffer\Files\File;

class DoubleQuotesSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
    use \MyPSR\Sniffs\UtilityTrait;
    
    public function register()
    {
        return array(T_CONSTANT_ENCAPSED_STRING);
    }

    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]["content"];

        if ($content[0] != "\"") {
            $fix = $phpcsFile->addFixableError(
                "Strings should be enclosed in double quotes",
                $stackPtr,
                "DoubleQuoteString"
            );

            if ($fix === true) {
                $newContent = substr(substr($content, 1), 0, -1);
                $newContent = str_replace("\"", "\\\"", $newContent);
                $newContent = str_replace("\\'", "'", $newContent);
                $newContent = str_replace("\$", "\\\$", $newContent);
                $newContent = str_replace("\\n", "\\\\n", $newContent);
                $newContent = str_replace("\\r", "\\\\r", $newContent);
                $newContent = str_replace("\\t", "\\\\t", $newContent);
                $newContent = str_replace("\\v", "\\\\v", $newContent);
                $newContent = str_replace("\\e", "\\\\e", $newContent);
                $newContent = str_replace("\\f", "\\\\f", $newContent);

                $newContent = preg_replace(
                    "/\\\\([0-7]{1,3})/",
                    "\\\\\\\\$1",
                    $newContent
                );

                $newContent = preg_replace(
                    "/\\\\(x[0-9A-Fa-f]{1,2})/",
                    "\\\\\\\\$1",
                    $newContent
                );

                $newContent = preg_replace(
                    "/\\\\(u[0-9A-Fa-f]+)/",
                    "\\\\\\\\$1",
                    $newContent
                );

                $phpcsFile->fixer->beginChangeset();
                $phpcsFile
                    ->fixer
                    ->replaceToken(
                        $stackPtr,
                        "\"$newContent\""
                    )
                ;
                $phpcsFile->fixer->endChangeset();
            }
        }
    }
}
