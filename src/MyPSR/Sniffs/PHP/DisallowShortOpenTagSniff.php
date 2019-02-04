<?php

namespace MyPSR\Sniffs\PHP;

/**
 * I PHP short open tags non devono essere usati a meno di <?=.
 *
 * Note:
 * 	- il tag <?= è ammesso in quanto supportato da php 5.4 in poi
 * 	  senza dover abilitare short_open_tag nel php.ini
 * 	- il tag <? di apertura invece non è ammesso in quanto costringe
 * 	  ad abilitare short_open_tag nel php.ini e quindi è scoraggiato
 * 	  per motivi di portabilità
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

	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
	{
		$this->setFile($phpcsFile);

		$content = $this->tokens[$stackPtr]["content"];

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
			$fix   = $this->file->addFixableError($error, $stackPtr, "Found", $data);
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

				$this->fixer->replaceToken($stackPtr, $newContent);
			}

			$this->file->recordMetric($stackPtr, "PHP short open tag used", "yes");
		} else {
			$this->file->recordMetric($stackPtr, "PHP short open tag used", "no");
		}
	}
}
