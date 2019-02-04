<?php

namespace MyPSR\Sniffs\Methods;

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

	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
	{
		$this->setFile($phpcsFile);

		$methodName = $this->file->getDeclarationName($stackPtr);
		if ($methodName === null) {
			// Ignora le closure
			return;
		}

		if ($this->file->hasCondition($stackPtr, T_FUNCTION) === true) {
			// Ignora le funzioni innestate
			return;
		}

		if ($this->file->hasCondition($stackPtr, array(T_CLASS, T_ANON_CLASS, T_TRAIT)) === false) {
			// Ignora le funzioni globali
			return;
		}

		$properties = $this->file->getMethodProperties($stackPtr);
		if ($properties["scope_specified"]) {
			return;
		} else {
			$error = sprintf("Visibility must be declared on method \"%s\"", $methodName);
			$fix   = $this->file->addFixableError($error, $stackPtr, "MissingVisibility");
			if ($fix === true) {
				$this->fixer->beginChangeset();
				$this->fixer->addContentBefore($stackPtr, "public ");
				$this->fixer->endChangeset();
			}
		}
	}

}
