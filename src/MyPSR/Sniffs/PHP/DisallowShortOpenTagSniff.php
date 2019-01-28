<?php

namespace MyPSR\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;

/**
 * Makes sure that shorthand PHP open tags are not used.
 *
 * Note:
 * 	- il tag <?= è ammesso in quanto supportato da php 5.4 in poi
 * 	  senza dover abilitare short_open_tag nel php.ini
 * 	- il tag <? di apertura invece non è ammesso in quanto costringe
 * 	  ad abilitare short_open_tag nel php.ini e quindi è scoraggiato
 * 	  per motivi di portabilità
 *
 * riferimento: http://php.net/manual/en/language.basic-syntax.phptags.php
 */
class DisallowShortOpenTagSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
    use \MyPSR\Sniffs\UtilityTrait;
    
    public function register()
    {
        return array(
            T_OPEN_TAG,
            T_INLINE_HTML
        );
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token
     *                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $content = $tokens[$stackPtr]["content"];

        if (strpos($content, "<?") === false) {
            return;
        }

        $pos = strpos($content, "<?");

        $echo = false;
        if (
            substr($content, $pos, 7) === "<? echo"
            || substr($content, $pos, 6) === "<?echo"
        ) {
            $echo = true;
        }

        $opening = false;
        if (
            empty($echo)
            && substr($content, $pos, 3) !== "<?="
            && substr($content, $pos, 5) !== "<?php"
            && substr($content, $pos, 2) === "<?"
        ) {
            $opening = true;
        }

        if ($echo || $opening) {
            $error = "Short PHP opening tag used; expected \"<?php\" or \"<?=\" but found \"%s\"";
            $data  = array($content);
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, "Found", $data);
            if ($fix === true) {
                if ($echo) {
                    if (strpos($content, "<? echo") !== false) {
                        $newContent = str_replace("<? echo", "<?=", $content);
                    } elseif (strpos($content, "<?echo") !== false) {
                        $newContent = str_replace("<?echo", "<?=", $content);
                    }
                } elseif ($opening) {
                    $thirdChar = substr($content, $pos + 2, 1);
                    if (!in_array($thirdChar, array(" ", "\t", "\n"))) {
                        $newContent = str_replace("<?", "<?php ", $content);
                    } else {
                        $newContent = str_replace("<?", "<?php", $content);
                    }
                }

                $phpcsFile->fixer->replaceToken($stackPtr, $newContent);
            }

            $phpcsFile->recordMetric($stackPtr, "PHP short open tag used", "yes");
        } else {
            $phpcsFile->recordMetric($stackPtr, "PHP short open tag used", "no");
        }
    }//end process()
}//end class
