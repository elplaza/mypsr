<?php

namespace MyPSR\Sniffs\ControlStructures;

/**
 * Tutte le strutture di controllo devono avere uno spazio
 * tra loro e le parentesi
 */
class SpacingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
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
        if (!$this->isValid($phpcsFile, $stackPtr)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        $token = $tokens[$stackPtr];

        if ($token["code"] !== T_DEFAULT) {
            // praticamente tutte hanno uno spazio dopo la keyword
            $this->oneSpaceAfter($phpcsFile, $stackPtr);
        }

        if ($this->isSwitchKeyword($phpcsFile, $stackPtr)) {
            $colon = $phpcsFile->findNext(array(T_COLON), $stackPtr);
            if ($this->isColon($phpcsFile, $colon)) {
                $this->noWhitespaceBefore($phpcsFile, $colon);
                $this->oneEolAfter($phpcsFile, $colon);
            }
        } else {

            // solo alcune hanno SICURAMENTE uno spazio prima
            if (in_array($token["code"], array(T_ELSE, T_ELSEIF, T_CATCH))) {
                $this->oneSpaceBefore($phpcsFile, $stackPtr);
            }

            // solo alcune hanno SICURAMENTE sia le parentesi tonde che quelle graffe
            if (
                in_array(
                    $token["code"],
                    array(T_IF, T_SWITCH, T_ELSEIF, T_FOR, T_FOREACH, T_CATCH)
                )
                && isset($token["parenthesis_closer"])
            ) {
                $this->oneSpaceAfter($phpcsFile, $token["parenthesis_closer"]);
            }

            // il while è particolare: può stare da solo o col do
            if ($token["code"] === T_WHILE) {
                if (!isset($token["scope_opener"])) {
                    // while nel do-while
                    $this->oneSpaceBefore($phpcsFile, $stackPtr);
                } elseif (isset($token["parenthesis_closer"])) {
                    // while da solo
                    $this->oneSpaceAfter($phpcsFile, $token["parenthesis_closer"]);
                }
            }
        }
    }
}
