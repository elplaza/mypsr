<?php

namespace MyPSR\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

trait UtilityTrait
{

	/**********************************************/
	/******************** TABS ********************/
	/**********************************************/

	/**
	 * Setta la larghezza (numero di spazi) di un tab.
	 * Priorità:
	 *  1) se passato come arg da linea di comando
	 *  2) se impostato nel ruleset.xml
	 *  3) quello di default della funzione
	 *
	 * @param  File $phpcsFile
	 * @param  int  $default
	 * @return int
	 */
	public function getTabWidth(File $phpcsFile, $default = 4)
	{
		$tabW = (!empty($phpcsFile->config->tabWidth))
			? $phpcsFile->config->tabWidth
			: $default
		;

		return intval($tabW);
	}

	/**********************************************/
	/******************* CHECK ********************/
	/**********************************************/

	/**
	 * Il ptr è valido?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isValid(File $phpcsFile, $ptr = null)
	{
		$tokens = $phpcsFile->getTokens();
		if (!is_null($ptr) && !empty($tokens) && !empty($tokens[$ptr])) {
			$token = $tokens[$ptr];
			if (
				isset($token["code"])
				&& isset($token["content"])
				&& isset($token["line"])
				&& isset($token["column"])
				&& isset($token["length"])
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * I $ptrs sono tutti validi?
	 *
	 * @param  File       $phpcsFile
	 * @param  array|null $ptrs
	 * @return boolean
	 */
	public function areValid(File $phpcsFile, $ptrs = null)
	{
		if (is_array($ptrs) && !empty($ptrs)) {
			foreach ($ptrs as $ptr) {
				if (!$this->isValid($phpcsFile, $ptr)) {
					return false;
				}
			}

			return true;
		}

		return false;
	}

	/**********************************************/
	/******************* PRINT ********************/
	/**********************************************/

	/**
	 * Stampa l'id e il token dei ptr passati
	 * interrompendo l'esecuzione.
	 * @param  File      $phpcsFile
	 * @param  int|array $ptrs
	 * @param  boolean   $type
	 * @return void
	 */
	public function dd(File $phpcsFile, $ptrs, $type = true)
	{
		$tokens = $phpcsFile->getTokens();

		$ptrs = (is_numeric($ptrs)) ? array($ptrs) : $ptrs;
		if (is_array($ptrs)) {
			$tmp = array();
			foreach ($ptrs as $ptr) {
				if ($this->isValid($phpcsFile, $ptr)) {
					$tmp[] = array_merge(array("ptr" => $ptr), $tokens[$ptr]);
				} else {
					$tmp[] = array("ptr" => $ptr, "error" => "invalid");
				}
			}

			if ($type) {
				var_dump($tmp);
			} else {
				print_r($tmp);
			}
			die();
		}

		die(var_dump($ptrs));
	}

	/**
	 * Stampa l'id e il token dei ptr nell'intervallo
	 * indicato interrompendo l'esecuzione.
	 * @param  File $phpcsFile
	 * @param  int  $start
	 * @param  int  $end
	 * @return void
	 */
	public function ddi(File $phpcsFile, $start = null, $end = null, $type = true)
	{
		if ($this->areValid($phpcsFile, array($start, $end))) {
			$this->dd($phpcsFile, range($start, $end), $type);
		}

		die(var_dump("invalid interval: start = $start, end = $end"));

	}

	/**********************************************/
	/**************** TYPE UTILITY ****************/
	/**********************************************/

	/**
	 * Il tipo del token è tra quelli passati?
	 * @param  File  $phpcsFile
	 * @param  array $types
	 * @param  int   $ptr
	 * @return boolean
	 */
	private function isType(File $phpcsFile, $types, $ptr = null)
	{
		if ($this->isValid($phpcsFile, $ptr)) {
			$tokens = $phpcsFile->getTokens();
			return in_array($tokens[$ptr]["code"], $types);
		}

		return false;
	}

	/**
	 * Dammi i tipi assegnazione
	 * @return array
	 */
	public function getAssignment()
	{
		return Tokens::$assignmentTokens;
	}

	/**
	 * Dammi i tipi confronto
	 * @return array
	 */
	public function getEquality()
	{
		return Tokens::$equalityTokens;
	}

	/**
	 * Dammi i tipi "codice non valido" (spazi e commenti)
	 * @return array
	 */
	public function getNoCode()
	{
		return Tokens::$emptyTokens;
	}

	/**
	 * Dammi i tipi "commento"
	 * @return array
	 */
	public function getComments()
	{
		return Tokens::$commentTokens;
	}

	/**
	 * Dammi i tipi operatore aritmetico
	 * @return array
	 */
	public function getArithmeticOperators()
	{
		return Tokens::$arithmeticTokens;
	}

	/**
	 * Dammi i tipi operatore
	 * @return array
	 */
	public function getOperators()
	{
		return Tokens::$operators;
	}

	/**
	 * Dammi i tipi operatore aritmetico
	 * @return array
	 */
	public function getBooleanOperators()
	{
		return Tokens::$booleanOperators;
	}

	/**
	 * Dammi i tipi parentesi di apertura (tonde)
	 * @return array
	 */
	public function getParenthesisOpeners()
	{
		return Tokens::$parenthesisOpeners;
	}

	/**
	 * Dammi i tipi di apertura di blocco
	 * @return array
	 */
	public function getBlockOpeners()
	{
		return Tokens::$blockOpeners;
	}

	/**
	 * Dammi tutti i tipi parentesi
	 * @return array
	 */
	public function getBrackets()
	{
		return Tokens::$bracketTokens;
	}

	/**
	 * Dammi tutti i tipi di parentesi di chiusura
	 * @return array
	 */
	public function getCloseBrackets()
	{
		return array_diff($this->getBrackets(), $this->getBlockOpeners());
	}

	/**
	 * Dammi tutti i tipi array
	 * @return array
	 */
	public function getArrays()
	{
		return array(T_ARRAY, T_OPEN_SHORT_ARRAY);
	}

	/**
	 * Dammi gli switch keywords
	 * @return array
	 */
	public function getSwitchKeywords()
	{
		return array(T_CASE, T_DEFAULT);
	}

	/**
	 * Dammi tutte le strutture di controllo
	 * @return array
	 */
	public function getControStructures()
	{
		return array(
			T_IF,
			T_SWITCH,
			T_WHILE,
			T_ELSE,
			T_ELSEIF,
			T_FOR,
			T_FOREACH,
			T_DO,
			T_TRY,
			T_CATCH
		);
	}

	/**
	 * E' un token di tipo end-of-line?
	 *
	 * Nota: un token di tipo end-of-line
	 *       significa che ha come contenuto
	 *       il carattere di eol ma può anche
	 *       essere che non ha solo quello
	 *
	 * @param  File    $phpcsFile
	 * @param  int     $ptr
	 * @param  boolean $strict contiene solo l'end-of-line?
	 * @return boolean
	 */
	public function isEol(File $phpcsFile, $ptr, $strict = false)
	{
		$tokens = $phpcsFile->getTokens();
		if ($this->isValid($phpcsFile, $ptr)) {
			if ($strict) {
				$token = $tokens[$ptr];
				return (
					$token["code"] === T_WHITESPACE
					&& $token["content"] === $phpcsFile->eolChar
					&& empty($token["length"])
				);
			} else {
				return (substr($tokens[$ptr]["content"], -1) == $phpcsFile->eolChar);
			}
		}

		return false;
	}

	/**
	 * E' un token di tipo start-of-line?
	 *
	 * @param  File    $phpcsFile
	 * @param  int     $ptr
	 * @return boolean
	 */
	public function isSol(File $phpcsFile, $ptr)
	{
		$tokens = $phpcsFile->getTokens();
		return ($this->isValid($phpcsFile, $ptr) && $tokens[$ptr]["column"] === 1);
	}

	/**
	 * E' un token di tipo virgola?
	 *
	 * @param  File    $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isComma(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, array(T_COMMA), $ptr);
	}

	/**
	 * E' un token di tipo whitespace?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isWhitespace(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, array(T_WHITESPACE), $ptr);
	}

	/**
	 * E' un token di tipo whitespace con uno spazio singolo?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isOneWhitespace(File $phpcsFile, $ptr = null)
	{
		if ($this->isWhitespace($phpcsFile, $ptr)) {
			$tokens = $phpcsFile->getTokens();
			return ($tokens[$ptr]["content"] == " ");
		}

		return false;
	}

	/**
	 * E' un double arrow?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isDoubleArrow(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, array(T_DOUBLE_ARROW), $ptr);
	}

	/**
	 * E' l'operatore di chaining?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isObjectOperator(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, array(T_OBJECT_OPERATOR), $ptr);
	}

	/**
	 * E' l'operatore di concatenazione di stringa?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isConcatOperator(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, array(T_STRING_CONCAT), $ptr);
	}

	/**
	 * E' una stringa?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isString(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, Tokens::$stringTokens, $ptr);
	}

	/**
	 * E' il punto e virgola?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isSemicolon(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, array(T_SEMICOLON), $ptr);
	}

	/**
	 * E' il carattere ":"?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isColon(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, array(T_COLON), $ptr);
	}

	/**
	 * E' un token di tipo "codice non valido" (spazi o commenti)?
	 *
	 * @param  File    $phpcsFile
	 * @param  int     $ptr
	 * @return boolean
	 */
	public function isNoCode(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, $this->getNoCode(), $ptr);
	}

	/**
	 * E' un token di tipo assegnazione?
	 *
	 * @param  File    $phpcsFile
	 * @param  int     $ptr
	 * @return boolean
	 */
	public function isAssignment(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, $this->getAssignment(), $ptr);
	}

	/**
	 * E' un token di apertura blocco?
	 *
	 * @param  File    $phpcsFile
	 * @param  int     $ptr
	 * @return boolean
	 */
	public function isBlockOpener(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, $this->getBlockOpeners(), $ptr);
	}

	/**
	 * E' un token di chiusura blocco?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isCloseBracket(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, $this->getCloseBrackets(), $ptr);
	}

	/**
	 * E' un token di tipo commento?
	 *
	 * @param  File        $phpcsFile
	 * @param  int|null    $ptr
	 * @param  null|string $type oneline, multiline or both
	 * @return boolean
	 */
	public function isComment(File $phpcsFile, $ptr = null, $type = null)
	{
		$types   = array_diff($this->getComments(), Tokens::$phpcsCommentTokens);
		$oneline = array(T_COMMENT);
		switch ($type) {
			case "oneline":
				$types = $oneline;
				break;
			case "multiline":
				$types = array_diff($types, $oneline);
				break;
			default:
				break;
		}

		return $this->isType($phpcsFile, $types, $ptr);
	}

	/**
	 * E' un token di tipo operatore?
	 *
	 * @param  File  $phpcsFile
	 * @param  int   $ptr
	 * @return boolean
	 */
	public function isOperator(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, $this->getOperators(), $ptr);
	}

	/**
	 * E' un token di tipo operatore booleano?
	 *
	 * @param  File  $phpcsFile
	 * @param  int   $ptr
	 * @return boolean
	 */
	public function isBooleanOperator(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, $this->getBooleanOperators(), $ptr);
	}

	/**
	 * E' un token di tipo operatore aritmetico?
	 *
	 * @param  File  $phpcsFile
	 * @param  int   $ptr
	 * @return boolean
	 */
	public function isArithmeticOperator(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, $this->getArithmeticOperators(), $ptr);
	}

	/**
	 * E' un token di tipo array?
	 *
	 * @param  File      $phpcsFile
	 * @param  int |null $ptr
	 * @return boolean
	 */
	public function isArray(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, $this->getArrays(), $ptr);
	}

	/**
	 * E' un token tipo "case" o "default"?
	 *
	 * @param  File      $phpcsFile
	 * @param  int |null $ptr
	 * @return boolean
	 */
	public function isSwitchKeyword(File $phpcsFile, $ptr = null)
	{
		return $this->isType($phpcsFile, $this->getSwitchKeywords(), $ptr);
	}

	/**********************************************/
	/****************** FIND TYPE *****************/
	/**********************************************/

	/**
	 * Trovami il primo codice valido partendo da $start fino a $end compresi.
	 *
	 * Note:
	 *  - il token $start è compreso nella ricerca quindi, se è già un codice valido,
	 *    allora il token restituito sarà proprio $start
	 *  - se il token $end è null allora cerca fino alla fine, altrimenti fino a $end
	 *
	 * @param  File     $phpcsFile
	 * @param  int      $start
	 * @param  int|null $end
	 * @return int|null
	 */
	public function nextCode(File $phpcsFile, $start, $end = null)
	{
		$nvc = null;
		if ($this->isValid($phpcsFile, $start)) {
			if (
				(is_numeric($end) && $end > $start)
				|| is_null($end)
			) {
				$end = (is_numeric($end)) ? $end + 1 : null;
				$nvc = $phpcsFile->findNext($this->getNoCode(), $start, $end, true);
			} elseif ($end === $start && !$this->isNoCode($phpcsFile, $start)) {
				$nvc = $start;
			}
		}

		return (is_numeric($nvc)) ? $nvc : null;
	}

	/**
	 * Trovami il primo codice valido partendo da $start e andando a ritroso fino a $end compresi.
	 * Note:
	 *  - il token $start è compreso nella ricerca quindi, se è già un codice valido,
	 *    allora il token restituito sarà proprio $start
	 *  - se il token $end è null allora cerca a ritroso fino all'inizio, altrimenti fino a $end
	 *
	 * @param  File     $phpcsFile
	 * @param  int      $start
	 * @param  int|null $end
	 * @return int|null
	 */
	public function prevCode(File $phpcsFile, $start, $end = null)
	{
		$pvc = null;
		if ($this->isValid($phpcsFile, $start)) {
			if (
				(is_numeric($end) && $end < $start)
				|| is_null($end)
			) {
				$end = (is_numeric($end)) ? $end - 1 : null;
				$pvc = $phpcsFile->findPrevious($this->getNoCode(), $start, $end, true);
			} elseif ($end === $start && !$this->isNoCode($phpcsFile, $start)) {
				$pvc = $start;
			}
		}

		return (is_numeric($pvc)) ? $pvc : null;
	}

	/**
	 * Trovami il primo commento partendo da $start e andando a ritroso fino a $end compresi.
	 *
	 * Note:
	 *  - il token $start è compreso nella ricerca quindi, se è già un commento,
	 *    allora il token restituito sarà proprio $start
	 *  - se il token $end è null allora cerca a ritroso fino all'inizio, altrimenti fino a $end
	 * @param  File     $phpcsFile
	 * @param  int      $start
	 * @param  int|null $end
	 * @return int|null
	 */
	public function prevComment(File $phpcsFile, $start, $end = null)
	{
		$prev = null;
		if ($this->isValid($phpcsFile, $start)) {
			if (
				(is_numeric($end) && $end < $start)
				|| is_null($end)
			) {
				$end  = (is_numeric($end)) ? $end - 1 : null;
				$prev = $phpcsFile->findPrevious($this->getComments(), $start, $end);
			} elseif ($end === $start && !$this->isComment($phpcsFile, $start)) {
				$prev = $start;
			}
		}

		return (is_numeric($prev)) ? $prev : null;
	}

	/**********************************************/
	/****************** FIND LINE *****************/
	/**********************************************/

	/**
	 * Trovami il token start-of-line della riga dove sta il token $ptr
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function findSol(File $phpcsFile, $ptr = null)
	{
		$sol = null;
		if ($this->isValid($phpcsFile, $ptr)) {
			$sol = $ptr;
			while ($this->isValid($phpcsFile, $sol - 1) && !$this->isSol($phpcsFile, $sol)) {
				$sol--;
			}
		}

		return $sol;
	}

	/**
	 * Trovami il token end-of-line della riga dove sta il token $ptr
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function findEol(File $phpcsFile, $ptr = null)
	{
		$eol = null;
		if ($this->isValid($phpcsFile, $ptr)) {
			$eol = $ptr;
			while ($this->isValid($phpcsFile, $eol + 1) && !$this->isEol($phpcsFile, $eol)) {
				$eol++;
			}
		}

		return $eol;
	}

	/**
	 * Trovami l'ultimo token di tipo codice (end-code-of-line) sulla riga dove sta il token $ptr
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function findEcol(File $phpcsFile, $ptr = null)
	{
		if ($this->isValid($phpcsFile, $ptr)) {
			$sol = $this->findSol($phpcsFile, $ptr);
			$eol = $this->findEol($phpcsFile, $ptr);

			if (!is_null($sol) && !is_null($eol)) {
				for ($i = $eol; $i >= $sol; $i--) {
					if (!$this->isNoCode($phpcsFile, $i)) {
						return $i;
					}
				}
			}
		}
	}

	/**
	 * Trovami il primo token di tipo codice (start-code-of-line) sulla riga dove sta il token $ptr
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function findScol(File $phpcsFile, $ptr = null)
	{
		if ($this->isValid($phpcsFile, $ptr)) {
			$sol = $this->findSol($phpcsFile, $ptr);
			$eol = $this->findEol($phpcsFile, $ptr);
			if (!is_null($sol) && !is_null($eol)) {
				for ($i = $sol; $i <= $eol; $i++) {
					if (!$this->isNoCode($phpcsFile, $i)) {
						return $i;
					}
				}
			}
		}
	}

	/**
	 * $ptr è il primo codice della riga?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isScol(File $phpcsFile, $ptr = null)
	{
		$scol = $this->findScol($phpcsFile, $ptr);
		return (!is_null($scol) && $ptr === $scol);
	}

	/**
	 * Trovami il start-code-of-line partendo
	 * dalla riga precedente a $ptr
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function prevScol(File $phpcsFile, $ptr = null)
	{
		if ($this->isValid($phpcsFile, $ptr)) {
			$scol = $this->findScol($phpcsFile, $ptr);
			if (!is_null($scol)) {
				for ($i = $scol - 1; $i >= 0; $i--) {
					if ($this->isScol($phpcsFile, $i)) {
						return $i;
					}
				}
			}
		}
	}

	/**
	 * Trovami il end-code-of-line partendo
	 * dalla riga precedente a $ptr
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function prevEcol(File $phpcsFile, $ptr = null)
	{
		if ($this->isValid($phpcsFile, $ptr)) {
			$scol = $this->findScol($phpcsFile, $ptr);
			if (!is_null($scol)) {
				for ($i = $scol - 1; $i >= 0; $i--) {
					if (!$this->isNoCode($phpcsFile, $i)) {
						return $i;
					}
				}
			}
		}
	}

	/**********************************************/
	/*************** OTHERS UTILITY ***************/
	/**********************************************/

	/**
	 * Quanto è lungo il token $ptr?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int
	 */
	public function getLength(File $phpcsFile, $ptr = null)
	{
		if ($this->isValid($phpcsFile, $ptr)) {
			$tokens = $phpcsFile->getTokens();
			return $tokens[$ptr]["length"];
		}

		return 0;
	}

	/**
	 * Di quante "unità" sta dentro il token $ptr?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int
	 */
	public function getUnits(File $phpcsFile, $ptr = null)
	{
		if ($this->isValid($phpcsFile, $ptr)) {
			$tokens = $phpcsFile->getTokens();
			return $tokens[$ptr]["column"] - 1;
		}

		return 0;
	}

	/**
	 * Di quante "unità" è indentato il token $ptr?
	 *
	 * Nota: l'indentazione comprende solo gli spazi bianchi
	 *       partendo dal start-code-of-line di $ptr
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int
	 */
	public function getWhitespaces(File $phpcsFile, $ptr = null)
	{
		$scol = $this->findScol($phpcsFile, $ptr);
		if (!is_null($scol)) {
			return $this->getUnits($phpcsFile, $scol);
		}

		return 0;
	}

	/**
	 * Quanto è lunga la riga dove sta il token $ptr?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int
	 */
	public function getLineLength(File $phpcsFile, $ptr = null)
	{
		$eol = $this->findEol($phpcsFile, $ptr);
		if (!is_null($eol)) {
			return $this->getUnits($phpcsFile, $eol) + $this->getLength($phpcsFile, $eol);
		}

		return 0;
	}

	/**
	 * I due token stanno sulla stessa riga?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr1
	 * @param  int|null $ptr2
	 * @return boolean
	 */
	public function isSameLine(File $phpcsFile, $ptr1 = null, $ptr2 = null)
	{
		if ($this->areValid($phpcsFile, array($ptr1, $ptr2))) {
			$tokens = $phpcsFile->getTokens();
			return ($tokens[$ptr1]["line"] === $tokens[$ptr2]["line"]);
		}

		return false;
	}

	/**
	 * La riga dove sta $ptr è vuota?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 */
	public function isEmptyLine(File $phpcsFile, $ptr = null)
	{
		$scol = $this->findScol($phpcsFile, $ptr);
		$ecol = $this->findEcol($phpcsFile, $ptr);

		return (is_null($scol) && is_null($ecol));
	}

	/**
	 * Dammi l'opener
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function getOpener(File $phpcsFile, $ptr = null)
	{
		if ($this->isValid($phpcsFile, $ptr) && $this->isCloseBracket($phpcsFile, $ptr)) {
			$tokens = $phpcsFile->getTokens();
			$token  = $tokens[$ptr];
			if (isset($token["parenthesis_opener"])) {
				return $token["parenthesis_opener"];
			} elseif (isset($token["bracket_opener"])) {
				return $token["bracket_opener"];
			}
		}
	}

	/**
	 * Dammi il closer
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function getCloser(File $phpcsFile, $ptr = null)
	{
		if ($this->isValid($phpcsFile, $ptr) && $this->isBlockOpener($phpcsFile, $ptr)) {
			$tokens = $phpcsFile->getTokens();
			$token  = $tokens[$ptr];
			if (isset($token["parenthesis_closer"])) {
				return $token["parenthesis_closer"];
			} elseif (isset($token["bracket_closer"])) {
				return $token["bracket_closer"];
			}
		}
	}

	/**********************************************/
	/*************** ARRAYS UTILITY ***************/
	/**********************************************/

	/**
	 * E' un array in notazione short ?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isShortArray(File $phpcsFile, $ptr = null)
	{
		if ($this->isArray($phpcsFile, $ptr)) {
			$tokens = $phpcsFile->getTokens();
			return ($tokens[$ptr]["code"] === T_OPEN_SHORT_ARRAY);
		}

		return false;
	}

	/**
	 * E' un array in notazione long ?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isLongArray(File $phpcsFile, $ptr = null)
	{
		if ($this->isArray($phpcsFile, $ptr)) {
			$tokens = $phpcsFile->getTokens();
			return ($tokens[$ptr]["code"] === T_ARRAY);
		}

		return false;
	}

	/**
	 * Dammi il token con la parentesi di apertura dell'array.
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function getArrayOpenParenthesis(File $phpcsFile, $ptr = null)
	{
		if ($this->isArray($phpcsFile, $ptr)) {
			$tokens = $phpcsFile->getTokens();
			if ($this->isLongArray($phpcsFile, $ptr)) {
				return $tokens[$ptr]["parenthesis_opener"];
			} elseif ($this->isShortArray($phpcsFile, $ptr)) {
				return $ptr;
			}
		}
	}

	/**
	 * Dammi il token con la parentesi di chiusura dell'array.
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function getArrayCloseParenthesis(File $phpcsFile, $ptr = null)
	{
		if ($this->isArray($phpcsFile, $ptr)) {
			$tokens = $phpcsFile->getTokens();
			$open   = $this->getArrayOpenParenthesis($phpcsFile, $ptr);
			if ($this->isLongArray($phpcsFile, $ptr)) {
				return $tokens[$open]["parenthesis_closer"];
			} elseif ($this->isShortArray($phpcsFile, $ptr)) {
				return $tokens[$open]["bracket_closer"];
			}
		}
	}

	/**
	 * E' un array single-line?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isSingleLineArray(File $phpcsFile, $ptr = null)
	{
		if ($this->isArray($phpcsFile, $ptr)) {
			$open  = $this->getArrayOpenParenthesis($phpcsFile, $ptr);
			$close = $this->getArrayCloseParenthesis($phpcsFile, $ptr);

			if (!is_null($open) && !is_null($close) && $close > $open) {
				return ($this->isSameLine($phpcsFile, $open, $close));
			}
		}

		return false;
	}

	/**
	 * E' un array multi-line?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isMultiLineArray(File $phpcsFile, $ptr = null)
	{
		if ($this->isArray($phpcsFile, $ptr)) {
			$open  = $this->getArrayOpenParenthesis($phpcsFile, $ptr);
			$close = $this->getArrayCloseParenthesis($phpcsFile, $ptr);

			if (!is_null($open) && !is_null($close) && $close > $open) {
				return (!$this->isSameLine($phpcsFile, $open, $close));
			}
		}

		return false;
	}

	/**
	 * E' un array senza codice valido all'interno?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isEmptyArray(File $phpcsFile, $ptr = null)
	{
		if ($this->isArray($phpcsFile, $ptr)) {
			$open  = $this->getArrayOpenParenthesis($phpcsFile, $ptr);
			$close = $this->getArrayCloseParenthesis($phpcsFile, $ptr);

			if (!is_null($open) && !is_null($close) && $close > $open) {
				$nextCode = $this->nextCode($phpcsFile, $open + 1, $close);
				return ($nextCode === $close);
			}
		}

		return false;
	}

	/**
	 * Dammi le doppie frecce "valide" (quelle che stanno tra chiave e valore
	 * degli elementi dell'array, non quelle innestate insomma).
	 *
	 * @param  File       $phpcsFile
	 * @param  int|null   $ptr
	 * @return array|null array con i token delle doppie frecce "valide"
	 */
	public function getArrayValidDoubleArrows(File $phpcsFile, $ptr = null)
	{
		if ($this->isArray($phpcsFile, $ptr) && !$this->isEmptyArray($phpcsFile, $ptr)) {
			$open   = $this->getArrayOpenParenthesis($phpcsFile, $ptr);
			$close  = $this->getArrayCloseParenthesis($phpcsFile, $ptr);
			if (!is_null($open) && !is_null($close) && $close > $open) {
				$arrows = array();
				$tokens = $phpcsFile->getTokens();
				for ($i = ($open + 1); $i < $close; $i++) {
					if (
						$this->isDoubleArrow($phpcsFile, $i)
						&& isset($tokens[$i]["nested_parenthesis"])
					) {
						$keys = array_keys($tokens[$i]["nested_parenthesis"]);
						if (end($keys) === $open) {
							$arrows[] = $i;
						}
					}
				}

				return $arrows;
			}
		}
	}

	/**
	 * Dammi le virgole "valide" (quelle che delimitano gli elementi dell'array,
	 * non innestate insomma).
	 *
	 * @param  File       $phpcsFile
	 * @param  int|null   $ptr
	 * @return array|null array con i token delle doppie frecce "valide"
	 */
	public function getArrayValidCommas(File $phpcsFile, $ptr = null)
	{
		if ($this->isArray($phpcsFile, $ptr) && !$this->isEmptyArray($phpcsFile, $ptr)) {
			$open   = $this->getArrayOpenParenthesis($phpcsFile, $ptr);
			$close  = $this->getArrayCloseParenthesis($phpcsFile, $ptr);
			if (!is_null($open) && !is_null($close) && $close > $open) {
				$commas = array();
				$tokens = $phpcsFile->getTokens();
				for ($i = ($open + 1); $i < $close; $i++) {
					if ($this->isComma($phpcsFile, $i)) {
						$token = $tokens[$i];
						if (isset($token["nested_parenthesis"])) {
							$keys = array_keys($token["nested_parenthesis"]);
						}

						if (
							(isset($token["nested_parenthesis"]) && end($keys) === $open)
							|| !isset($token["nested_parenthesis"])
						) {
							// escludo l'ultima virgola
							$code = $this->nextCode($phpcsFile, ($i + 1), $close);
							if ($code !== $close) {
								$commas[] = $i;
							}
						}
					}
				}

				return $commas;
			}
		}
	}

	/**********************************************/
	/************** CHAINING UTILITY **************/
	/**********************************************/

	/**
	 * è il primo operatore di chaining?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isFirstChaining(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			$prev = $this->prevChain($phpcsFile, $ptr);
			if (is_null($prev)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * dammi il token che apre l'intero chaining
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function chainingStart(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			do {
				$start = $this->startChain($phpcsFile, $ptr);
				$ptr   = $this->prevChain($phpcsFile, $ptr);
			} while ($this->isObjectOperator($phpcsFile, $ptr));

			return $start;
		}
	}

	/**
	 * dammi il token che chiude l'intero chaining
	 *
	 * Nota: se l'ultimo chain ha le parentesi, il token
	 * che chiude l'intero chaining sarà la rispettiva parentesi
	 * di apertura. Questo perché il chaining è considerato multiline
	 * se ha almeno anche l'ultima parentesi di apertura a capo.
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function chainingEnd(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			do {
				$end = $this->endChain($phpcsFile, $ptr);
				$ptr = $this->nextChain($phpcsFile, $ptr);
			} while ($this->isObjectOperator($phpcsFile, $ptr));

			if ($this->isCloseBracket($phpcsFile, $end)) {
				return $this->getOpener($phpcsFile, $end);
			}

			return $end;
		}
	}

	/**
	 * dammi il token che apre il chaining di $ptr
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function startChain(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			$sos    = $phpcsFile->findStartOfStatement($ptr);
			$prev1  = $this->prevCode($phpcsFile, $ptr - 1, $sos);
			if ($this->isCloseBracket($phpcsFile, $prev1)) {
				$opener = $this->getOpener($phpcsFile, $prev1);
				$prev1  = $this->prevCode($phpcsFile, $opener - 1, $sos);
			}

			$tokens = $phpcsFile->getTokens();
			return in_array($tokens[$prev1]["code"], array(T_STRING, T_VARIABLE)) ? $prev1 : $sos;
		}
	}

	/**
	 * dammi il chaining operator precedente a $ptr
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function prevChain(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			$startPtrChain = $this->startChain($phpcsFile, $ptr);
			if ($this->isValid($phpcsFile, $startPtrChain)) {
				$sos     = $phpcsFile->findStartOfStatement($ptr);
				$prevOpr = $this->prevCode($phpcsFile, $startPtrChain - 1, $sos);
				if ($this->isObjectOperator($phpcsFile, $prevOpr)) {
					return $prevOpr;
				}
			}
		}
	}

	/**
	 * dammi il token che chiude il chaining di $ptr
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function endChain(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			$eos    = $phpcsFile->findEndOfStatement($ptr);
			$next1  = $this->nextCode($phpcsFile, $ptr + 1, $eos);
			$tokens = $phpcsFile->getTokens();
			if (in_array($tokens[$next1]["code"], array(T_STRING, T_VARIABLE))) {
				$next2 = $this->nextCode($phpcsFile, $next1 + 1, $eos);
				if ($this->isBlockOpener($phpcsFile, $next2)) {
					return $this->getCloser($phpcsFile, $next2);
				} else {
					return $next1;
				}
			}
		}
	}

	/**
	 * dammi il chaining operator successivo a $ptr
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function nextChain(File $phpcsFile, $ptr = null)
	{
		if ($this->isObjectOperator($phpcsFile, $ptr)) {
			$endPtrChain = $this->endChain($phpcsFile, $ptr);
			if ($this->isValid($phpcsFile, $endPtrChain)) {
				$eos     = $phpcsFile->findEndOfStatement($ptr);
				$nextOpr = $this->nextCode($phpcsFile, $endPtrChain + 1, $eos);
				if ($this->isObjectOperator($phpcsFile, $nextOpr)) {
					return $nextOpr;
				}
			}
		}
	}

	/**********************************************/
	/**************** FIXER UTILITY ***************/
	/**********************************************/

	/**
	 * $ptr è minuscolo?
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function toLowercase(File $phpcsFile, $ptr = null)
	{
		if ($this->isValid($phpcsFile, $ptr) && !$this->isNoCode($phpcsFile, $ptr)) {
			$tokens   = $phpcsFile->getTokens();
			$content  = $tokens[$ptr]["content"];
			$expected = strtolower(trim($content));
			if ($content !== $expected) {
				$fix = $phpcsFile->addFixableError(
					"It should be lowercase: expected \"%s\" but found \"%s\"",
					$ptr,
					"NotLowerCase",
					array($expected, $content)
				);
				if ($fix === true) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($ptr, $expected);
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}

	/**
	 * $ptr deve avere uno e un solo spazio
	 * tra lui e il successivo codice valido.
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 */
	public function oneSpaceAfter(File $phpcsFile, $ptr = null)
	{
		if ($this->areValid($phpcsFile, array($ptr, $ptr + 1))) {
			$nextCode = $this->nextCode($phpcsFile, $ptr + 1);
			if (!is_null($nextCode)) {
				if ($nextCode != $ptr + 2 || !$this->isOneWhitespace($phpcsFile, $ptr + 1)) {
					$tokens = $phpcsFile->getTokens();
					$fix    = $phpcsFile->addFixableError(
						"One and only one space expected after \"%s\"",
						$ptr,
						"OneSpaceAfter",
						array($tokens[$ptr]["content"])
					);

					if ($fix === true) {
						$phpcsFile->fixer->beginChangeset();
						if ($nextCode > $ptr + 1) {
							for ($i = $ptr + 1; $i < $nextCode; $i++) {
								$phpcsFile->fixer->replaceToken($i, "");
							}
						}

						$phpcsFile->fixer->addContent($ptr, " ");
						$phpcsFile->fixer->endChangeset();
					}
				}
			}
		}
	}

	/**
	 * $ptr deve avere uno e un solo spazio
	 * tra lui e il precedente codice valido.
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 */
	public function oneSpaceBefore(File $phpcsFile, $ptr = null)
	{
		if ($this->areValid($phpcsFile, array($ptr, $ptr - 1))) {
			$prevCode = $this->prevCode($phpcsFile, $ptr - 1);
			if (!is_null($prevCode)) {
				if ($prevCode != $ptr - 2 || !$this->isOneWhitespace($phpcsFile, $ptr - 1)) {
					$tokens = $phpcsFile->getTokens();
					$fix    = $phpcsFile->addFixableError(
						"One and only one space expected before \"%s\"",
						$ptr,
						"OneSpaceBefore",
						array($tokens[$ptr]["content"])
					);

					if ($fix === true) {
						$phpcsFile->fixer->beginChangeset();
						if ($prevCode < $ptr - 1) {
							for ($i = $ptr - 1; $i > $prevCode; $i--) {
								$phpcsFile->fixer->replaceToken($i, "");
							}
						}

						$phpcsFile->fixer->addContent($prevCode, " ");
						$phpcsFile->fixer->endChangeset();
					}
				}
			}
		}
	}

	/**
	 * tra $ptr e il precedente e successivo
	 * codice valido ci dev'essere solo uno spazio.
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 */
	public function oneSpaceAround(File $phpcsFile, $ptr = null)
	{
		$this->oneSpaceBefore($phpcsFile, $ptr);
		$this->oneSpaceAfter($phpcsFile, $ptr);
	}

	/**
	 * $ptr non deve avere neanche uno spazio dopo.
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 */
	public function noWhitespaceAfter(File $phpcsFile, $ptr = null)
	{
		if ($this->areValid($phpcsFile, array($ptr, $ptr + 1))) {
			$nextCode = $this->nextCode($phpcsFile, $ptr + 1);
			if (!is_null($nextCode) && $nextCode != $ptr + 1) {
				$tokens = $phpcsFile->getTokens();
				$fix    = $phpcsFile->addFixableError(
					"No space expected after \"%s\"",
					$ptr,
					"NoSpaceAfter",
					array($tokens[$ptr]["content"])
				);

				if ($fix === true) {
					$phpcsFile->fixer->beginChangeset();
					if ($nextCode > $ptr + 1) {
						for ($i = $ptr + 1; $i < $nextCode; $i++) {
							$phpcsFile->fixer->replaceToken($i, "");
						}
					}
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}

	/**
	 * $ptr non deve avere neanche uno spazio prima.
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 */
	public function noWhitespaceBefore(File $phpcsFile, $ptr = null)
	{
		if ($this->areValid($phpcsFile, array($ptr, $ptr - 1))) {
			$prevCode = $this->prevCode($phpcsFile, $ptr - 1);
			if (!is_null($prevCode) && $prevCode != $ptr - 1) {
				$tokens = $phpcsFile->getTokens();
				$fix    = $phpcsFile->addFixableError(
					"No space expected before \"%s\"",
					$ptr,
					"NoSpaceBefore",
					array($tokens[$ptr]["content"])
				);

				if ($fix === true) {
					$phpcsFile->fixer->beginChangeset();
					if ($prevCode < $ptr - 1) {
						for ($i = $ptr - 1; $i > $prevCode; $i--) {
							$phpcsFile->fixer->replaceToken($i, "");
						}
					}
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}

	/**
	 * $ptr deve avere un eolChar dopo.
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 */
	public function oneEolAfter(File $phpcsFile, $ptr = null)
	{
		if (
			$this->areValid($phpcsFile, array($ptr, $ptr + 1))
			&& !$this->isEol($phpcsFile, $ptr + 1, true)
		) {
			$tokens = $phpcsFile->getTokens();
			$fix    = $phpcsFile->addFixableError(
				"After \"%s\" is expected only the eol",
				$ptr,
				"OneEolAfter",
				array($tokens[$ptr]["content"])
			);

			if ($fix === true) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addNewline($ptr);
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * $ptr deve essere il primo codice valido della riga.
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 */
	public function startCodeOfLine(File $phpcsFile, $ptr = null)
	{
		if ($this->isValid($phpcsFile, $ptr) && !$this->isScol($phpcsFile, $ptr)) {
			$tokens = $phpcsFile->getTokens();

			$fix = $phpcsFile->addFixableError(
				"\"%s\" is expected the first code in this line",
				$ptr,
				"FirstLineCode",
				array($tokens[$ptr]["content"])
			);

			if ($fix === true) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addNewlineBefore($ptr);
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * Se la riga dove sta $ptr è vuota, la rimuove.
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 */
	public function removeEmptyLine(File $phpcsFile, $ptr = null)
	{
		if ($this->isValid($phpcsFile, $ptr) && $this->isEmptyLine($phpcsFile, $ptr)) {
			$sol = $this->findSol($phpcsFile, $ptr);
			$eol = $this->findEol($phpcsFile, $ptr);

			if (!is_null($sol) && !is_null($eol)) {
				$fix = $phpcsFile->addFixableError(
					"Empty rows is not admitted, to remove",
					$ptr,
					"NoEmptyLine"
				);

				if ($fix === true) {
					$phpcsFile->fixer->beginChangeset();
					for ($i = $sol; $i <= $eol; $i++) {
						$phpcsFile->fixer->replaceToken($i, "");
					}
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}

	/**
	 * Rimuove le righe vuote tra $start e $end.
	 *
	 * @param  File $phpcsFile
	 * @param  int  $start
	 * @param  int  $end
	 */
	public function removeEmptyLines(File $phpcsFile, $start, $end)
	{
		if ($this->areValid($phpcsFile, array($start, $end)) && $start < $end) {
			for ($i = $start + 1; $i < $end; $i++) {
				if ($this->isSol($phpcsFile, $i)) {
					$this->removeEmptyLine($phpcsFile, $i);
				}
			}
		}
	}

	/**********************************************/
	/************* INDENTATION UTILITY ************/
	/**********************************************/

	/**
	 * Verifica e fixa l'indentazione di $ptr.
	 *
	 * Calcola l'indentazione di $ptr e la confronta
	 * con quella attesa data da $indentation e $additionalTabs
	 *
	 * @param  File     $phpcsFile
	 * @param  int|null $ptr
	 * @param  integer  $indentation
	 * @param  integer  $additionalTabs
	 * @return null
	 */
	public function checkIndentation(
		File $phpcsFile,
		$ptr = null,
		$indentation = 0,
		$additionalTabs = 0
	) {
		if ($this->isScol($phpcsFile, $ptr) && $indentation >= 0) {
			$tabW    = $this->getTabWidth($phpcsFile);
			$aSpaces = $additionalTabs * $tabW;
			$iPtr    = $this->getWhitespaces($phpcsFile, $ptr);
			if ($iPtr !== ($indentation + $aSpaces)) {
				$tokens = $phpcsFile->getTokens();

				$fix = $phpcsFile->addFixableError(
					"\"%s\" is not indented correctly",
					$ptr,
					"IncorrectIndentation",
					array($tokens[$ptr]["content"])
				);

				if ($fix === true) {
					$tabs   = floor($indentation / $tabW) + $additionalTabs;
					$spaces = $indentation % $tabW;

					$string = str_repeat("\t", $tabs) . str_repeat(" ", $spaces);

					$sol = $this->findSol($phpcsFile, $ptr);
					$phpcsFile->fixer->beginChangeset();
					if ($ptr > $sol) {
						for ($i = $sol; $i < $ptr; $i++) {
							$phpcsFile->fixer->replaceToken($i, "");
						}
					}

					$phpcsFile->fixer->addContentBefore($ptr, $string);
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}

}
