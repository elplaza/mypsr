<?php

namespace MyPSR\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;

/**
 * Gli array multi line devono:
 * - non avere una virgola dopo l'ultimo elemento
 * - non avere spazi prima delle virgole
 * - avere ogni elemento su una riga nuova
 * - avere uno spazio dopo le =>
 * - avere, se completamente associativi, gli elementi e le => allineate
 */
class MultiLineSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
	use \MyPSR\Sniffs\UtilityTrait;

	public function register()
	{
		return $this->getArrays();
	}

	public function process(File $phpcsFile, $stackPtr)
	{
		$this->setFile($phpcsFile);

		if (!$this->isEmptyArray($stackPtr) && $this->isMultiLineArray($stackPtr)) {
			// processo tutte le virgole "valide"
			$commas = $this->getArrayValidCommas($stackPtr);
			if (!empty($commas)) {
				foreach ($commas as $comma) {
					// prima di ogni virgola "valida" non ci devono essere spazi
					$this->noWhitespaceBefore($comma);
					// dopo la virgola ci dev'essere un eol
					$this->oneEolAfter($comma);
				}
			}

			$arrows = $this->getArrayValidDoubleArrows($stackPtr);
			if (!empty($arrows)) {
				// mi assicuro lo spazio dopo le doppie frecce "valide"
				foreach ($arrows as $arrow) {
					$this->oneSpaceAfter($arrow);
				}
			}

			// se c'è, elimino la virgola dell'ultimo elemento
			$open     = $this->getArrayOpenParenthesis($stackPtr);
			$close    = $this->getArrayCloseParenthesis($stackPtr);
			$lastCode = $this->prevCode($close - 1, $open);
			if ($this->isComma($lastCode)) {
				$fix = $this->file->addFixableError(
					"Last comma should be removed",
					$lastCode,
					"NoLastComma"
				);

				if ($fix === true) {
					$this->fixer->beginChangeset();
					$this->fixer->replaceToken($lastCode, "");
					$this->fixer->endChangeset();
				}
			}

			// solo per gli array totalmente associativi con più di un valore
			// vanno allineate le doppie frecce
			if (count($commas) > 0 && count($arrows) === (count($commas) + 1)) {
				// allinea le doppie frecce
				$this->alignDoubleArrows($open, $close, $commas, $arrows);
			}
		}
	}

	/**
	 * Allinea le double arrows dell'array
	 * @param  int   $open      indice del token di apertura parentesi
	 * @param  int   $close     indice del token di chiusura parentesi
	 * @param  array $commas    array con le virgole valide
	 * @param  array $arrows    array con le double arrows
	 * @return void
	 */
	private function alignDoubleArrows($open, $close, $commas, $arrows)
	{
		$keys      = array();
		$maxLength = 1;
		$count     = count($arrows);
		$start     = $open;
		// vado a vedere la chiave più lunga
		for ($i = 0; $i < $count; $i++) {
			$info = array("arrow" => $arrows[$i]);

			$codeInfo = $this->codeInfo($start + 1, $arrows[$i] - 1);
			$keys[]   = array_merge($codeInfo, $info);
			if ($codeInfo["length"] > $maxLength) {
				$maxLength = $codeInfo["length"];
			}

			if (isset($commas[$i])) {
				$start = $commas[$i];
			}
		}

		$maxLength++; // aggiungo lo spazio
		foreach ($keys as $key) {
			$arrow = $key["arrow"];
			$evc   = $key["end"];
			$lvc   = $key["length"];

			if (empty($lvc)) {
				continue;
			}

			// spazi che ci dovrebbero essere
			$spaces = str_repeat(" ", $maxLength - $lvc);

			$content = "";
			for ($i = ($evc + 1); $i < $arrow; $i++) {
				$content .= $this->tokens[$i]["content"];
			}

			if ($content != $spaces) {
				$error = "Double arrow in associative array declaration must been aligned";
				$fix   = $this->file->addFixableError(
					$error,
					$arrow,
					"DoubleArrowNotAligned"
				);
				if ($fix === true) {
					$this->fixer->beginChangeset();
					for ($i = ($evc + 1); $i < $arrow; $i++) {
						$this->fixer->replaceToken($i, "");
					}
					$this->fixer->addContent($evc, $spaces);
					$this->fixer->endChangeset();
				}
			}
		}
	}

	/**
	 * calcola la lunghezza del codice valido tra i token $start e $end
	 */
	private function codeInfo($start, $end)
	{
		$svc    = false;
		$evc    = false;
		$length = 0;
		if ($end >= $start) {
			// cerco il primo codice valido
			$svc = $start;
			for ($i = $start; $i <= $end; $i++) {
				if ($this->isComma($i) || $this->isNoCode($i)) {
					continue;
				} else {
					$svc = $i;
					break;
				}
			}

			// cerco l'ultimo codice valido
			$evc = $end;
			for ($i = $end; $i >= $start; $i--) {
				if ($this->isComma($i) || $this->isNoCode($i)) {
					continue;
				} else {
					$evc = $i;
					break;
				}
			}

			// calcolo la lunghezza del codice
			if ($evc >= $svc) {
				$eToken = $this->tokens[$evc];
				$sToken = $this->tokens[$svc];
				$length = $eToken["column"] + $eToken["length"] - $sToken["column"];
			}
		}

		return array(
			"start"  => $svc,
			"end"    => $evc,
			"length" => $length
		);
	}

}
