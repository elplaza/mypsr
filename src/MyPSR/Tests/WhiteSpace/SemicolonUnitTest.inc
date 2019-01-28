<?php

$test = 5 + 6 * 4     ;

$test = "asdasdasdasd"
	. "asdasdasdasdasd";

$test = "asdasdasdasd"
	. "asdasdasd"
	;

$test = 5 * 3 + 5 + getNumber()
;

$test = "asdasd" . "asdasdasd"
    ;

if ($cond) {
	$test = "asdasdasd" . "asdasdasd"
	;
}

switch($cond) {
	case "a":
		$test = "asdasd"
			. "asdasdasd";
		break;

	default:
		$test = "asdasdasd"
		;
		break;
}

if($this->method($arg)){ //Comment
	$test = sprintf(
		$this->doSomething('string %s'),
		$stuff->getName()
	);
}


function ciao() {

	 $test = "asdasdasdasd"
		. "asdasdasdasdasd"
	   ;

		do {
			something();
		} while ($condition)   ;
}
