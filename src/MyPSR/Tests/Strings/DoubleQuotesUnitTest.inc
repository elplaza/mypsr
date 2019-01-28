<?php

$test = 'this is a test string';
$test2 = 'this is a \'complex\' test string';
$test3 = 'this is a mixed'
	. " test string"
	. ' with some hard "stuff"'
;

$test4 = 'this $test should be escaped';
$test5 = 'this \n should be escaped as well as this \t and this \r';
$test6 = '\n \f \t \v \e \r \d \d \043 \x3A \x3b $ \u092a \u94AB \asdasd \daadad';
