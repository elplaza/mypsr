<?php

namespace MyPSR\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

trait UtilityTrait
{
	protected $file;
	protected $tokens;
	protected $fixer;

	public function setFile(File $phpcsFile)
	{
		$this->file   = $phpcsFile;
		$this->tokens = $phpcsFile->getTokens();
		$this->fixer  = $phpcsFile->fixer;
		return $this;
	}

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
	 * @param  int $default
	 * @return int
	 */
	public function getTabWidth($default = 4)
	{
		$tabW = (!empty($this->file->config->tabWidth))
			? $this->file->config->tabWidth
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
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isValid($ptr = null)
	{
		if (!is_null($ptr) && !empty($this->tokens) && !empty($this->tokens[$ptr])) {
			$token = $this->tokens[$ptr];
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
	 * @param  array|null $ptrs
	 * @return boolean
	 */
	public function areValid($ptrs = null)
	{
		if (is_array($ptrs) && !empty($ptrs)) {
			foreach ($ptrs as $ptr) {
				if (!$this->isValid($ptr)) {
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
	 *
	 * @param  int|array $ptrs
	 * @param  boolean   $type
	 * @return void
	 */
	public function dd($ptrs, $type = true)
	{
		$ptrs = (is_numeric($ptrs)) ? array($ptrs) : $ptrs;
		if (is_array($ptrs)) {
			$tmp = array();
			foreach ($ptrs as $ptr) {
				if ($this->isValid($ptr)) {
					$tmp[] = array_merge(array("ptr" => $ptr), $this->tokens[$ptr]);
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
	 *
	 * @param  int  $start
	 * @param  int  $end
	 * @return void
	 */
	public function ddi($start = null, $end = null, $type = true)
	{
		if ($this->areValid(array($start, $end))) {
			$this->dd(range($start, $end), $type);
		}

		die(var_dump("invalid interval: start = $start, end = $end"));
	}

	/**********************************************/
	/**************** TYPE UTILITY ****************/
	/**********************************************/

	/**
	 * Il tipo del token è tra quelli passati?
	 *
	 * @param  array $types
	 * @param  int   $ptr
	 * @return boolean
	 */
	private function isType($types, $ptr = null)
	{
		if ($this->isValid($ptr)) {
			return in_array($this->tokens[$ptr]["code"], $types);
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
	 * Dammi i token dell'operatore ternario
	 * @return array
	 */
	public function getTernary()
	{
		return array(T_INLINE_THEN);
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
	 * @param  int     $ptr
	 * @param  boolean $strict contiene solo l'end-of-line?
	 * @return boolean
	 */
	public function isEol($ptr, $strict = false)
	{
		if ($this->isValid($ptr)) {
			if ($strict) {
				$token = $this->tokens[$ptr];
				return (
					$token["code"] === T_WHITESPACE
					&& $token["content"] === $this->file->eolChar
					&& empty($token["length"])
				);
			} else {
				return (substr($this->tokens[$ptr]["content"], -1) == $this->file->eolChar);
			}
		}

		return false;
	}

	/**
	 * E' un token di tipo start-of-line?
	 *
	 * @param  int     $ptr
	 * @return boolean
	 */
	public function isSol($ptr)
	{
		return ($this->isValid($ptr) && $this->tokens[$ptr]["column"] === 1);
	}

	/**
	 * E' un token di tipo virgola?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isComma($ptr = null)
	{
		return $this->isType(array(T_COMMA), $ptr);
	}

	/**
	 * E' un token di tipo whitespace?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isWhitespace($ptr = null)
	{
		return $this->isType(array(T_WHITESPACE), $ptr);
	}

	/**
	 * E' un token di tipo whitespace con uno spazio singolo?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isOneWhitespace($ptr = null)
	{
		if ($this->isWhitespace($ptr)) {
			return ($this->tokens[$ptr]["content"] == " ");
		}

		return false;
	}

	/**
	 * E' un double arrow?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isDoubleArrow($ptr = null)
	{
		return $this->isType(array(T_DOUBLE_ARROW), $ptr);
	}

	/**
	 * E' l'operatore di chaining?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isObjectOperator($ptr = null)
	{
		return $this->isType(array(T_OBJECT_OPERATOR), $ptr);
	}

	/**
	 * E' l'operatore di concatenazione di stringa?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isConcatOperator($ptr = null)
	{
		return $this->isType(array(T_STRING_CONCAT), $ptr);
	}

	/**
	 * E' una stringa?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isString($ptr = null)
	{
		return $this->isType(Tokens::$stringTokens, $ptr);
	}

	/**
	 * E' il punto e virgola?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isSemicolon($ptr = null)
	{
		return $this->isType(array(T_SEMICOLON), $ptr);
	}

	/**
	 * E' il carattere ":"?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isColon($ptr = null)
	{
		return $this->isType(array(T_COLON), $ptr);
	}

	/**
	 * E' un token di tipo "codice non valido" (spazi o commenti)?
	 *
	 * @param  int     $ptr
	 * @return boolean
	 */
	public function isNoCode($ptr = null)
	{
		return $this->isType($this->getNoCode(), $ptr);
	}

	/**
	 * E' un token di tipo assegnazione?
	 *
	 * @param  int     $ptr
	 * @return boolean
	 */
	public function isAssignment($ptr = null)
	{
		return $this->isType($this->getAssignment(), $ptr);
	}

	/**
	 * E' un token di apertura blocco?
	 *
	 * @param  int     $ptr
	 * @return boolean
	 */
	public function isBlockOpener($ptr = null)
	{
		return $this->isType($this->getBlockOpeners(), $ptr);
	}

	/**
	 * E' un token di chiusura blocco?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isCloseBracket($ptr = null)
	{
		return $this->isType($this->getCloseBrackets(), $ptr);
	}

	/**
	 * E' un token di tipo commento?
	 *
	 * @param  int|null    $ptr
	 * @param  null|string $type oneline, multiline or both
	 * @return boolean
	 */
	public function isComment($ptr = null, $type = null)
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

		return $this->isType($types, $ptr);
	}

	/**
	 * E' un token di tipo operatore?
	 *
	 * @param  int   $ptr
	 * @return boolean
	 */
	public function isOperator($ptr = null)
	{
		return $this->isType($this->getOperators(), $ptr);
	}

	/**
	 * E' un token di tipo operatore booleano?
	 *
	 * @param  int   $ptr
	 * @return boolean
	 */
	public function isBooleanOperator($ptr = null)
	{
		return $this->isType($this->getBooleanOperators(), $ptr);
	}

	/**
	 * E' un token di tipo operatore aritmetico?
	 *
	 * @param  int   $ptr
	 * @return boolean
	 */
	public function isArithmeticOperator($ptr = null)
	{
		return $this->isType($this->getArithmeticOperators(), $ptr);
	}

	/**
	 * E' un token di tipo operatore ternario?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isTernary($ptr = null)
	{
		return $this->isType($this->getTernary(), $ptr);
	}

	/**
	 * E' un token di tipo array?
	 *
	 * @param  int |null $ptr
	 * @return boolean
	 */
	public function isArray($ptr = null)
	{
		return $this->isType($this->getArrays(), $ptr);
	}

	/**
	 * E' un token tipo "case" o "default"?
	 *
	 * @param  int |null $ptr
	 * @return boolean
	 */
	public function isSwitchKeyword($ptr = null)
	{
		return $this->isType($this->getSwitchKeywords(), $ptr);
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
	 * @param  int      $start
	 * @param  int|null $end
	 * @return int|null
	 */
	public function nextCode($start, $end = null)
	{
		$nvc = null;
		if ($this->isValid($start)) {
			if ((is_numeric($end) && $end > $start) || is_null($end)) {
				$end = (is_numeric($end)) ? $end + 1 : null;
				$nvc = $this->file->findNext($this->getNoCode(), $start, $end, true);
			} elseif ($end === $start && !$this->isNoCode($start)) {
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
	 * @param  int      $start
	 * @param  int|null $end
	 * @return int|null
	 */
	public function prevCode($start, $end = null)
	{
		$pvc = null;
		if ($this->isValid($start)) {
			if ((is_numeric($end) && $end < $start) || is_null($end)) {
				$end = (is_numeric($end)) ? $end - 1 : null;
				$pvc = $this->file->findPrevious($this->getNoCode(), $start, $end, true);
			} elseif ($end === $start && !$this->isNoCode($start)) {
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
	 *
	 * @param  int      $start
	 * @param  int|null $end
	 * @return int|null
	 */
	public function prevComment($start, $end = null)
	{
		$prev = null;
		if ($this->isValid($start)) {
			if ((is_numeric($end) && $end < $start) || is_null($end)) {
				$end  = (is_numeric($end)) ? $end - 1 : null;
				$prev = $this->file->findPrevious($this->getComments(), $start, $end);
			} elseif ($end === $start && !$this->isComment($start)) {
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
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function findSol($ptr = null)
	{
		$sol = null;
		if ($this->isValid($ptr)) {
			$sol = $ptr;
			while ($this->isValid($sol - 1) && !$this->isSol($sol)) {
				$sol--;
			}
		}

		return $sol;
	}

	/**
	 * Trovami il token end-of-line della riga dove sta il token $ptr
	 *
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function findEol($ptr = null)
	{
		$eol = null;
		if ($this->isValid($ptr)) {
			$eol = $ptr;
			while ($this->isValid($eol + 1) && !$this->isEol($eol)) {
				$eol++;
			}
		}

		return $eol;
	}

	/**
	 * Trovami l'ultimo token di tipo codice (end-code-of-line) sulla riga dove sta il token $ptr
	 *
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function findEcol($ptr = null)
	{
		if ($this->isValid($ptr)) {
			$sol = $this->findSol($ptr);
			$eol = $this->findEol($ptr);

			if (!is_null($sol) && !is_null($eol)) {
				for ($i = $eol; $i >= $sol; $i--) {
					if (!$this->isNoCode($i)) {
						return $i;
					}
				}
			}
		}
	}

	/**
	 * Trovami il primo token di tipo codice (start-code-of-line) sulla riga dove sta il token $ptr
	 *
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function findScol($ptr = null)
	{
		if ($this->isValid($ptr)) {
			$sol = $this->findSol($ptr);
			$eol = $this->findEol($ptr);
			if (!is_null($sol) && !is_null($eol)) {
				for ($i = $sol; $i <= $eol; $i++) {
					if (!$this->isNoCode($i)) {
						return $i;
					}
				}
			}
		}
	}

	/**
	 * $ptr è il primo codice della riga?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isScol($ptr = null)
	{
		$scol = $this->findScol($ptr);
		return (!is_null($scol) && $ptr === $scol);
	}

	/**
	 * Trovami il start-code-of-line partendo
	 * dalla riga precedente a $ptr
	 *
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function prevScol($ptr = null)
	{
		if ($this->isValid($ptr)) {
			$scol = $this->findScol($ptr);
			if (!is_null($scol)) {
				for ($i = $scol - 1; $i >= 0; $i--) {
					if ($this->isScol($i)) {
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
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function prevEcol($ptr = null)
	{
		if ($this->isValid($ptr)) {
			$scol = $this->findScol($ptr);
			if (!is_null($scol)) {
				for ($i = $scol - 1; $i >= 0; $i--) {
					if (!$this->isNoCode($i)) {
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
	 * @param  int|null $ptr
	 * @return int
	 */
	public function getLength($ptr = null)
	{
		if ($this->isValid($ptr)) {
			return $this->tokens[$ptr]["length"];
		}

		return 0;
	}

	/**
	 * Di quante "unità" sta dentro il token $ptr?
	 *
	 * @param  int|null $ptr
	 * @return int
	 */
	public function getUnits($ptr = null)
	{
		if ($this->isValid($ptr)) {
			return $this->tokens[$ptr]["column"] - 1;
		}

		return 0;
	}

	/**
	 * Di quante "unità" è indentato il token $ptr?
	 *
	 * Nota: l'indentazione comprende solo gli spazi bianchi
	 *       partendo dal start-code-of-line di $ptr
	 *
	 * @param  int|null $ptr
	 * @return int
	 */
	public function getWhitespaces($ptr = null)
	{
		$scol = $this->findScol($ptr);
		if (!is_null($scol)) {
			return $this->getUnits($scol);
		}

		return 0;
	}

	/**
	 * Quanto è lunga la riga dove sta il token $ptr?
	 *
	 * @param  int|null $ptr
	 * @return int
	 */
	public function getLineLength($ptr = null)
	{
		$eol = $this->findEol($ptr);
		if (!is_null($eol)) {
			return $this->getUnits($eol) + $this->getLength($eol);
		}

		return 0;
	}

	/**
	 * I due token stanno sulla stessa riga?
	 *
	 * @param  int|null $ptr1
	 * @param  int|null $ptr2
	 * @return boolean
	 */
	public function isSameLine($ptr1 = null, $ptr2 = null)
	{
		if ($this->areValid(array($ptr1, $ptr2))) {
			return ($this->tokens[$ptr1]["line"] === $this->tokens[$ptr2]["line"]);
		}

		return false;
	}

	/**
	 * La riga dove sta $ptr è vuota?
	 *
	 * @param int|null $ptr
	 */
	public function isEmptyLine($ptr = null)
	{
		$scol = $this->findScol($ptr);
		$ecol = $this->findEcol($ptr);

		return (is_null($scol) && is_null($ecol));
	}

	/**
	 * Dammi l'opener
	 *
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function getOpener($ptr = null)
	{
		if ($this->isValid($ptr) && $this->isCloseBracket($ptr)) {
			$token = $this->tokens[$ptr];
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
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function getCloser($ptr = null)
	{
		if ($this->isValid($ptr) && $this->isBlockOpener($ptr)) {
			$token = $this->tokens[$ptr];
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
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isShortArray($ptr = null)
	{
		if ($this->isArray($ptr)) {
			return ($this->tokens[$ptr]["code"] === T_OPEN_SHORT_ARRAY);
		}

		return false;
	}

	/**
	 * E' un array in notazione long ?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isLongArray($ptr = null)
	{
		if ($this->isArray($ptr)) {
			return ($this->tokens[$ptr]["code"] === T_ARRAY);
		}

		return false;
	}

	/**
	 * Dammi il token con la parentesi di apertura dell'array.
	 *
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function getArrayOpenParenthesis($ptr = null)
	{
		if ($this->isArray($ptr)) {
			if ($this->isLongArray($ptr)) {
				return $this->tokens[$ptr]["parenthesis_opener"];
			} elseif ($this->isShortArray($ptr)) {
				return $ptr;
			}
		}
	}

	/**
	 * Dammi il token con la parentesi di chiusura dell'array.
	 *
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function getArrayCloseParenthesis($ptr = null)
	{
		if ($this->isArray($ptr)) {
			$open = $this->getArrayOpenParenthesis($ptr);
			if ($this->isLongArray($ptr)) {
				return $this->tokens[$open]["parenthesis_closer"];
			} elseif ($this->isShortArray($ptr)) {
				return $this->tokens[$open]["bracket_closer"];
			}
		}
	}

	/**
	 * E' un array single-line?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isSingleLineArray($ptr = null)
	{
		if ($this->isArray($ptr)) {
			$open  = $this->getArrayOpenParenthesis($ptr);
			$close = $this->getArrayCloseParenthesis($ptr);

			if (!is_null($open) && !is_null($close) && $close > $open) {
				return ($this->isSameLine($open, $close));
			}
		}

		return false;
	}

	/**
	 * E' un array multi-line?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isMultiLineArray($ptr = null)
	{
		if ($this->isArray($ptr)) {
			$open  = $this->getArrayOpenParenthesis($ptr);
			$close = $this->getArrayCloseParenthesis($ptr);

			if (!is_null($open) && !is_null($close) && $close > $open) {
				return (!$this->isSameLine($open, $close));
			}
		}

		return false;
	}

	/**
	 * E' un array senza codice valido all'interno?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isEmptyArray($ptr = null)
	{
		if ($this->isArray($ptr)) {
			$open  = $this->getArrayOpenParenthesis($ptr);
			$close = $this->getArrayCloseParenthesis($ptr);

			if (!is_null($open) && !is_null($close) && $close > $open) {
				$nextCode = $this->nextCode($open + 1, $close);
				return ($nextCode === $close);
			}
		}

		return false;
	}

	/**
	 * Dammi le doppie frecce "valide" (quelle che stanno tra chiave e valore
	 * degli elementi dell'array, non quelle innestate insomma).
	 *
	 * @param  int|null   $ptr
	 * @return array|null array con i token delle doppie frecce "valide"
	 */
	public function getArrayValidDoubleArrows($ptr = null)
	{
		if ($this->isArray($ptr) && !$this->isEmptyArray($ptr)) {
			$open  = $this->getArrayOpenParenthesis($ptr);
			$close = $this->getArrayCloseParenthesis($ptr);
			if (!is_null($open) && !is_null($close) && $close > $open) {
				$arrows = array();
				for ($i = ($open + 1); $i < $close; $i++) {
					if (
						$this->isDoubleArrow($i)
						&& isset($this->tokens[$i]["nested_parenthesis"])
					) {
						$keys = array_keys($this->tokens[$i]["nested_parenthesis"]);
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
	 * @param  int|null   $ptr
	 * @return array|null array con i token delle doppie frecce "valide"
	 */
	public function getArrayValidCommas($ptr = null)
	{
		if ($this->isArray($ptr) && !$this->isEmptyArray($ptr)) {
			$open  = $this->getArrayOpenParenthesis($ptr);
			$close = $this->getArrayCloseParenthesis($ptr);
			if (!is_null($open) && !is_null($close) && $close > $open) {
				$commas = array();
				for ($i = ($open + 1); $i < $close; $i++) {
					if ($this->isComma($i)) {
						$token = $this->tokens[$i];
						if (isset($token["nested_parenthesis"])) {
							$keys = array_keys($token["nested_parenthesis"]);
						}

						if (
							(isset($token["nested_parenthesis"]) && end($keys) === $open)
							|| !isset($token["nested_parenthesis"])
						) {
							// escludo l'ultima virgola
							$code = $this->nextCode(($i + 1), $close);
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
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isFirstChaining($ptr = null)
	{
		if ($this->isObjectOperator($ptr)) {
			$prev = $this->prevChain($ptr);
			if (is_null($prev)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * dammi il token che apre l'intero chaining
	 *
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function chainingStart($ptr = null)
	{
		if ($this->isObjectOperator($ptr)) {
			do {
				$start = $this->startChain($ptr);
				$ptr   = $this->prevChain($ptr);
			} while ($this->isObjectOperator($ptr));

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
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function chainingEnd($ptr = null)
	{
		if ($this->isObjectOperator($ptr)) {
			do {
				$end = $this->endChain($ptr);
				$ptr = $this->nextChain($ptr);
			} while ($this->isObjectOperator($ptr));

			if ($this->isCloseBracket($end)) {
				return $this->getOpener($end);
			}

			return $end;
		}
	}

	/**
	 * dammi il token che apre il chaining di $ptr
	 *
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function startChain($ptr = null)
	{
		if ($this->isObjectOperator($ptr)) {
			$sos    = $this->file->findStartOfStatement($ptr);
			$prev1  = $this->prevCode($ptr - 1, $sos);
			if ($this->isCloseBracket($prev1)) {
				$opener = $this->getOpener($prev1);
				$prev1  = $this->prevCode($opener - 1, $sos);
			}

			return in_array($this->tokens[$prev1]["code"], array(T_STRING, T_VARIABLE))
				? $prev1
				: $sos
			;
		}
	}

	/**
	 * dammi il chaining operator precedente a $ptr
	 *
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function prevChain($ptr = null)
	{
		if ($this->isObjectOperator($ptr)) {
			$startPtrChain = $this->startChain($ptr);
			if ($this->isValid($startPtrChain)) {
				$sos     = $this->file->findStartOfStatement($ptr);
				$prevOpr = $this->prevCode($startPtrChain - 1, $sos);
				if ($this->isObjectOperator($prevOpr)) {
					return $prevOpr;
				}
			}
		}
	}

	/**
	 * dammi il token che chiude il chaining di $ptr
	 *
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function endChain($ptr = null)
	{
		if ($this->isObjectOperator($ptr)) {
			$eos    = $this->file->findEndOfStatement($ptr);
			$next1  = $this->nextCode($ptr + 1, $eos);
			if (in_array($this->tokens[$next1]["code"], array(T_STRING, T_VARIABLE))) {
				$next2 = $this->nextCode($next1 + 1, $eos);
				if ($this->isBlockOpener($next2)) {
					return $this->getCloser($next2);
				} else {
					return $next1;
				}
			}
		}
	}

	/**
	 * dammi il chaining operator successivo a $ptr
	 *
	 * @param  int|null $ptr
	 * @return int|null
	 */
	public function nextChain($ptr = null)
	{
		if ($this->isObjectOperator($ptr)) {
			$endPtrChain = $this->endChain($ptr);
			if ($this->isValid($endPtrChain)) {
				$eos     = $this->file->findEndOfStatement($ptr);
				$nextOpr = $this->nextCode($endPtrChain + 1, $eos);
				if ($this->isObjectOperator($nextOpr)) {
					return $nextOpr;
				}
			}
		}
	}

	/**
	 * il chaining di $ptr sta dentro un array?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function isInArray($ptr = null)
	{
		if ($this->isObjectOperator($ptr)) {
			if (isset($this->tokens[$ptr]["nested_parenthesis"])) {
				$parenthesis = array_keys($this->tokens[$ptr]["nested_parenthesis"]);
				$opener      = end($parenthesis);
				if ($this->isBlockOpener($opener)) {
					$prevCode = $this->prevCode($opener - 1);
					if ($this->isArray($prevCode)) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**********************************************/
	/**************** FIXER UTILITY ***************/
	/**********************************************/

	/**
	 * $ptr è minuscolo?
	 *
	 * @param  int|null $ptr
	 * @return boolean
	 */
	public function toLowercase($ptr = null)
	{
		if ($this->isValid($ptr) && !$this->isNoCode($ptr)) {
			$content  = $this->tokens[$ptr]["content"];
			$expected = strtolower(trim($content));
			if ($content !== $expected) {
				$fix = $this->file->addFixableError(
					"It should be lowercase: expected \"%s\" but found \"%s\"",
					$ptr,
					"NotLowerCase",
					array($expected, $content)
				);
				if ($fix === true) {
					$this->fixer->beginChangeset();
					$this->fixer->replaceToken($ptr, $expected);
					$this->fixer->endChangeset();
				}
			}
		}
	}

	/**
	 * $ptr deve avere uno e un solo spazio
	 * tra lui e il successivo codice valido.
	 *
	 * @param  int|null $ptr
	 */
	public function oneSpaceAfter($ptr = null)
	{
		if ($this->areValid(array($ptr, $ptr + 1))) {
			$nextCode = $this->nextCode($ptr + 1);
			if (!is_null($nextCode)) {
				if ($nextCode != $ptr + 2 || !$this->isOneWhitespace($ptr + 1)) {
					$fix = $this->file->addFixableError(
						"One and only one space expected after \"%s\"",
						$ptr,
						"OneSpaceAfter",
						array($this->tokens[$ptr]["content"])
					);

					if ($fix === true) {
						$this->fixer->beginChangeset();
						if ($nextCode > $ptr + 1) {
							for ($i = $ptr + 1; $i < $nextCode; $i++) {
								$this->fixer->replaceToken($i, "");
							}
						}

						$this->fixer->addContent($ptr, " ");
						$this->fixer->endChangeset();
					}
				}
			}
		}
	}

	/**
	 * $ptr deve avere uno e un solo spazio
	 * tra lui e il precedente codice valido.
	 *
	 * @param int|null $ptr
	 */
	public function oneSpaceBefore($ptr = null)
	{
		if ($this->areValid(array($ptr, $ptr - 1))) {
			$prevCode = $this->prevCode($ptr - 1);
			if (!is_null($prevCode)) {
				if ($prevCode != $ptr - 2 || !$this->isOneWhitespace($ptr - 1)) {
					$fix = $this->file->addFixableError(
						"One and only one space expected before \"%s\"",
						$ptr,
						"OneSpaceBefore",
						array($this->tokens[$ptr]["content"])
					);

					if ($fix === true) {
						$this->fixer->beginChangeset();
						if ($prevCode < $ptr - 1) {
							for ($i = $ptr - 1; $i > $prevCode; $i--) {
								$this->fixer->replaceToken($i, "");
							}
						}

						$this->fixer->addContent($prevCode, " ");
						$this->fixer->endChangeset();
					}
				}
			}
		}
	}

	/**
	 * tra $ptr e il precedente e successivo
	 * codice valido ci dev'essere solo uno spazio.
	 *
	 * @param int|null $ptr
	 */
	public function oneSpaceAround($ptr = null)
	{
		$this->oneSpaceBefore($ptr);
		$this->oneSpaceAfter($ptr);
	}

	/**
	 * $ptr non deve avere neanche uno spazio dopo.
	 *
	 * @param  int|null $ptr
	 */
	public function noWhitespaceAfter($ptr = null)
	{
		if ($this->areValid(array($ptr, $ptr + 1))) {
			$nextCode = $this->nextCode($ptr + 1);
			if (!is_null($nextCode) && $nextCode != $ptr + 1) {
				$fix = $this->file->addFixableError(
					"No space expected after \"%s\"",
					$ptr,
					"NoSpaceAfter",
					array($this->tokens[$ptr]["content"])
				);

				if ($fix === true) {
					$this->fixer->beginChangeset();
					if ($nextCode > $ptr + 1) {
						for ($i = $ptr + 1; $i < $nextCode; $i++) {
							$this->fixer->replaceToken($i, "");
						}
					}
					$this->fixer->endChangeset();
				}
			}
		}
	}

	/**
	 * $ptr non deve avere neanche uno spazio prima.
	 *
	 * @param  int|null $ptr
	 */
	public function noWhitespaceBefore($ptr = null)
	{
		if ($this->areValid(array($ptr, $ptr - 1))) {
			$prevCode = $this->prevCode($ptr - 1);
			if (!is_null($prevCode) && $prevCode != $ptr - 1) {
				$fix = $this->file->addFixableError(
					"No space expected before \"%s\"",
					$ptr,
					"NoSpaceBefore",
					array($this->tokens[$ptr]["content"])
				);

				if ($fix === true) {
					$this->fixer->beginChangeset();
					if ($prevCode < $ptr - 1) {
						for ($i = $ptr - 1; $i > $prevCode; $i--) {
							$this->fixer->replaceToken($i, "");
						}
					}
					$this->fixer->endChangeset();
				}
			}
		}
	}

	/**
	 * $ptr deve avere un eolChar dopo.
	 *
	 * @param  int|null $ptr
	 */
	public function oneEolAfter($ptr = null)
	{
		if ($this->areValid(array($ptr, $ptr + 1)) && !$this->isEol($ptr + 1, true)) {
			$fix = $this->file->addFixableError(
				"After \"%s\" is expected only the eol",
				$ptr,
				"OneEolAfter",
				array($this->tokens[$ptr]["content"])
			);

			if ($fix === true) {
				$this->fixer->beginChangeset();
				$this->fixer->addNewline($ptr);
				$this->fixer->endChangeset();
			}
		}
	}

	/**
	 * $ptr deve essere il primo codice valido della riga.
	 *
	 * @param  int|null $ptr
	 */
	public function startCodeOfLine($ptr = null)
	{
		if ($this->isValid($ptr) && !$this->isScol($ptr)) {
			$fix = $this->file->addFixableError(
				"\"%s\" is expected the first code in this line",
				$ptr,
				"FirstLineCode",
				array($this->tokens[$ptr]["content"])
			);

			if ($fix === true) {
				$this->fixer->beginChangeset();
				$this->fixer->addNewlineBefore($ptr);
				$this->fixer->endChangeset();
			}
		}
	}

	/**
	 * Se la riga dove sta $ptr è vuota, la rimuove.
	 *
	 * @param  int|null $ptr
	 */
	public function removeEmptyLine($ptr = null)
	{
		if ($this->isValid($ptr) && $this->isEmptyLine($ptr)) {
			// evito le closures
			if ($this->file->hasCondition($ptr, T_CLOSURE) === true) {
				return;
			}

			$sol = $this->findSol($ptr);
			$eol = $this->findEol($ptr);

			if (!is_null($sol) && !is_null($eol)) {
				$fix = $this->file->addFixableError(
					"Empty rows is not admitted, to remove",
					$ptr,
					"NoEmptyLine"
				);

				if ($fix === true) {
					$this->fixer->beginChangeset();
					for ($i = $sol; $i <= $eol; $i++) {
						$this->fixer->replaceToken($i, "");
					}
					$this->fixer->endChangeset();
				}
			}
		}
	}

	/**
	 * Rimuove le righe vuote tra $start e $end.
	 *
	 * @param  int  $start
	 * @param  int  $end
	 */
	public function removeEmptyLines($start, $end)
	{
		if ($this->areValid(array($start, $end)) && $start < $end) {
			for ($i = $start + 1; $i < $end; $i++) {
				if ($this->isSol($i)) {
					$this->removeEmptyLine($i);
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
	 * @param  int|null $ptr
	 * @param  integer  $indentation
	 * @param  integer  $additionalTabs
	 * @return null
	 */
	public function checkIndentation($ptr = null, $indentation = 0, $additionalTabs = 0)
	{
		if ($this->isScol($ptr) && $indentation >= 0) {
			$tabW    = $this->getTabWidth();
			$aSpaces = $additionalTabs * $tabW;
			$iPtr    = $this->getWhitespaces($ptr);
			if ($iPtr !== ($indentation + $aSpaces)) {
				$fix = $this->file->addFixableError(
					"\"%s\" is not indented correctly",
					$ptr,
					"IncorrectIndentation",
					array($this->tokens[$ptr]["content"])
				);

				if ($fix === true) {
					$tabs   = floor($indentation / $tabW) + $additionalTabs;
					$spaces = $indentation % $tabW;

					$string = str_repeat("\t", $tabs) . str_repeat(" ", $spaces);

					$sol = $this->findSol($ptr);
					$this->fixer->beginChangeset();
					if ($ptr > $sol) {
						for ($i = $sol; $i < $ptr; $i++) {
							$this->fixer->replaceToken($i, "");
						}
					}

					$this->fixer->addContentBefore($ptr, $string);
					$this->fixer->endChangeset();
				}
			}
		}
	}

}
