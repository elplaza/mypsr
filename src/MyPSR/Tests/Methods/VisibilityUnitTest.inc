<?php

function ciao($arg){
	echo 'ciao';

	function pluto($arg){
		echo 'ciao';
	};
}

class Gianni{
	function giacomo($value)
	{
		function pippo($arg){
			echo 'ciao';
		};
		echo $value;
	}

	public function a($a)
	{
		echo 'a';
	}

	protected function b($b)
	{
		echo 'b';
	}

	private function c($c)
	{
		echo 'c';
	}

	static function s($s)
	{
		echo 's';
	}

	final function f($f)
	{
		echo 'f';
	}

	abstract function abs($abs);

}

abstract class Giannino{
	function abstgiacomo($value)
	{
		function pippo($arg){
			echo 'ciao';
		};
		echo $value;
	}

}

trait Gianna{
	function giacomo($value)
	{

		$a = function($arg) {
			return $arg * 2;
		};

		function pippo($arg){
			echo 'ciao';
		};
		echo $value;
	}

}

$util->setLogger(new class {
	function log($msg)
	{
		echo $msg;
	}
});
