<?php

$this->doStuff()->doMoreStuff(array("asdasd", "asdasdasd"));

$this
->doStuff()->doMoreStuff(array("asdasd", "asdasdasd"));

$this->doStuff("asdasdasdasd")
		->doMoreStuff(array("asdasd", "asdasdasd"));

$this      ->doStuff()
		->doFigata();

$this
	  ->doTest($this->doMore()->doThis("asdasdad"))
	->doSomeMore();

$nextSeparator = $phpcsFile->findNext(
		$find,
		($nextSeparator + 1),
		$parenthesis["close"]
	);

$expectedPadding = $this->getString(
	$expectedLevel,
	$this->getTabWidth($phpcsFile)
);

if (
	$info->getTelephone() != $data['telephone'] ||
	$info->getTelephonePrefix() != $prefix
) {
	// se ha cambiato il numero già presente lo setto a non verificato
	$info->setTelephoneVerification(null);
}
